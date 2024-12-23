<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent1Id = Menu::create([
            'name' => 'Setting',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 5,
        ])->id;

        $parent2Id = Menu::create([
            'name' => 'Master  Data',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 1,
        ])->id;

        $parent3Id = Menu::create([
            'name' => 'Sparepart Management',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 2,
        ])->id;

        $menu_report = Menu::create([
            'name' => 'Report',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 3,
        ])->id;

        $menu_tracking = Menu::create([
            'name' => 'Tracking And Monitoring',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 4,
        ])->id;

        Menu::insert([
            // ACL
            [
                'name' => 'Roles',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => '/role',
                'order' => 20,
            ],
            [
                'name' => 'Permissions',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => '/permision',
                'order' => 21,
            ],
            [
                'name' => 'Users',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => '/user',
                'order' => 22,
            ],
            [
                'name' => 'Menu',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => '/menu',
                'order' => 23,
            ],
            // MASTER DATA
            [
                'name' => 'Asset',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => '/asset',
                'order' => 6,
            ],
            [
                'name' => 'Management Project',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => '/management-project',
                'order' => 7,
            ],
            [
                'name' => 'Karyawan',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => '/employee',
                'order' => 8,
            ],
            // INVENTORI
            [
                'name' => 'Gudang',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => '/werehouse',
                'order' => 9,
            ],
            [
                'name' => 'Kategori',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => '/category-item',
                'order' => 10,
            ],
            [
                'name' => 'Barang',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => '/item',
                'order' => 11,
            ],
            // report
            [
                'name' => 'Expanses Fuel',
                'icon' => null,
                'parent_id' => $menu_report,
                'route' => '/report-fuel',
                'order' => 12,
            ],
            [
                'name' => 'Asset Project',
                'icon' => null,
                'parent_id' => $menu_report,
                'route' => '/report-asset',
                'order' => 13,
            ],
            [
                'name' => 'Expenses Sparepart',
                'icon' => null,
                'parent_id' => $menu_report,
                'route' => '/report-sparepart',
                'order' => 14,
            ],
            [
                'name' => 'Total Loadsheet',
                'icon' => null,
                'parent_id' => $menu_report,
                'route' => '/report-loadsheet',
                'order' => 15,
            ],
            // monitoring
            [
                'name' => 'Fuel Consumption',
                'icon' => null,
                'parent_id' => $menu_tracking,
                'route' => '/fuel',
                'order' => 16,
            ],
            [
                'name' => 'Inspection Schedule',
                'icon' => null,
                'parent_id' => $menu_tracking,
                'route' => '/inspection-schedule',
                'order' => 17,
            ],
            [
                'name' => 'Fuel Stock',
                'icon' => null,
                'parent_id' => $menu_tracking,
                'route' => '/fuel-ipb',
                'order' => 18,
            ],
            [
                'name' => 'Loadsheet',
                'icon' => null,
                'parent_id' => $menu_tracking,
                'route' => '/loadsheet',
                'order' => 19,
            ],
        ]);
    }
}
