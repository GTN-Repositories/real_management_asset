<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(GeneralSettingSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(MenuPermissionSeeder::class);

        $permissions = [
            'view-role',
            'view-permision',
            'view-user',
            'view-employee',
            'view-werehouse',
            'view-category-item',
            'view-job-title',
            'view-item',
            'view-report-fuel',
            'view-report-asset',
            'view-report-sparepart',
            'view-report-loadsheet',
            'view-fuel',
            'view-fuel-ipb',

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
            'asset-show',
            'asset-import-excel',
            'asset-export-excel',

            'management-project-create',
            'management-project-edit',
            'management-project-delete',
            'management-project-import-excel',
            'management-project-export-excel',
            'management-project-request',
            'management-project-approve',
            'management-project-show',

            'employee-create',
            'employee-edit',
            'employee-delete',

            'werehouse-create',
            'werehouse-edit',
            'werehouse-delete',

            'category-item-create',
            'category-item-edit',
            'category-item-delete',

            'item-create',
            'item-edit',
            'item-delete',
            'item-import-excel',
            'item-export-excel',
            'item-request',
            'item-approve',
            'item-show',

            'report-fuel-create',
            'report-fuel-edit',
            'report-fuel-delete',
            'report-fuel-export-excel',
            'report-fuel-export-pdf',
            'report-fuel-export-excel-month',

            'report-asset-create',
            'report-asset-edit',
            'report-asset-delete',
            'report-asset-export-excel',

            'report-sparepart-create',
            'report-sparepart-edit',
            'report-sparepart-delete',

            'report-loadsheet-create',
            'report-loadsheet-edit',
            'report-loadsheet-delete',

            'fuel-create',
            'fuel-edit',
            'fuel-delete',
            'fuel-import-excel',
            'fuel-export-excel',
            'fuel-request',

            'inspection-schedule-create',
            'inspection-schedule-edit',
            'inspection-schedule-delete',
            'inspection-schedule-show',
            'inspection-schedule-create-maintenance',

            'fuel-ipb-create',
            'fuel-ipb-edit',
            'fuel-ipb-delete',

            'loadsheet-create',
            'loadsheet-edit',
            'loadsheet-delete',
            'loadsheet-import-excel',
            'loadsheet-export-excel',

            'view-driver',

            ' view-status-asset',
            'view-log-activity',
            'view-asset-reminder',
            'view-asset-attachment',
            'view-oum',
            'maintenances-create',
            'view-soil-type',
            'view-status-asset',
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

        Role::create([
            'name' => 'admin',
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

        $Herwin = User::create([
            'name' => 'Pak Herwin',
            'email' => 'herwin@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $Jimmy = User::create([
            'name' => 'Pak Jimmy',
            'email' => 'jimmy@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $Imelda = User::create([
            'name' => 'Pak Imelda',
            'email' => 'imelda@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $Benny = User::create([
            'name' => 'Pak Benny',
            'email' => 'benny@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $Dhika = User::create([
            'name' => 'Dhika',
            'email' => 'dhika@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $Tasya = User::create([
            'name' => 'Tasya',
            'email' => 'tasya@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('superAdmin');
        $driver->assignRole('driver');

        $Herwin->assignRole('superAdmin');
        $Jimmy->assignRole('superAdmin');
        $Imelda->assignRole('superAdmin');
        $Benny->assignRole('superAdmin');

        $Dhika->assignRole('admin');
        $Tasya->assignRole('admin');

        $admin->givePermissionTo(Permission::all());
        $driver->givePermissionTo('view-driver');

        $Herwin->givePermissionTo(Permission::all());

        $Jimmy->givePermissionTo(Permission::all());

        $Imelda->givePermissionTo(Permission::all());

        $Benny->givePermissionTo(Permission::all());


        $permissions = Permission::all()->filter(function ($permission) {
            return !Str::contains($permission->name, 'approve');
        });

        $Dhika->givePermissionTo($permissions);
        $Tasya->givePermissionTo($permissions);
    }
}
