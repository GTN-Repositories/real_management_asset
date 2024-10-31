<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create dashboard permission
        Permission::firstOrCreate(['name' => 'view-dashboard']);

        // Get all menus
        $menus = Helper::getMenu();

        foreach ($menus as $menu) {
            // Create permission for parent menu
            $permissionName = 'view-' . Str::slug($menu->name);
            Permission::firstOrCreate(['name' => $permissionName]);

            // Create permissions for child menus
            if ($menu->children->isNotEmpty()) {
                foreach ($menu->children as $child) {
                    $childPermission = 'view-' . Str::slug($child->name);
                    Permission::firstOrCreate(['name' => $childPermission]);
                }
            }
        }

        // Optional: Output created permissions
        $this->command->info('Permissions created successfully!');
    }
}
