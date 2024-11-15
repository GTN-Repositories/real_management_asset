<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MenuSeeder::class);
        $this->call(FormSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(MenuPermissionSeeder::class);

        $permissions = [
            'role-create',
            'role-edit',
            'role-delete',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'user-create',
            'user-edit',
            'user-delete',
            'menu-create',
            'menu-edit',
            'menu-delete',
            'asset-create',
            'asset-edit',
            'asset-delete',
            'management-create',
            'management-edit',
            'management-delete',
            'fuel-create',
            'fuel-edit',
            'fuel-delete',
            'monitoring-create',
            'monitoring-edit',
            'monitoring-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create([
            'name' => 'superAdmin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'driver',
            'guard_name' => 'web',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $driver = User::create([
            'name' => 'Supriman',
            'email' => 'driver@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('superAdmin');
        $driver->assignRole('driver');

        $admin->givePermissionTo(Permission::all());
    }
}
