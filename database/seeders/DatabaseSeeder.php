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

        $permissions = [
            'role create',
            'role read',
            'role update',
            'role delete',
            'permission create',
            'permission read',
            'permission update',
            'permission delete',
            'user create',
            'user read',
            'user update',
            'user delete',
            'kendaraan create',
            'kendaraan read',
            'kendaraan update',
            'kendaraan delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        Role::create([
            'name' => 'superAdmin',
            'guard_name' => 'web',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('superAdmin');
    }
}
