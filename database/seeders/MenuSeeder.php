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
            'name' => 'ACL',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 1,
        ])->id;

        $parent2Id = Menu::create([
            'name' => 'Master  Data',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 6,
        ])->id;

        $parent3Id = Menu::create([
            'name' => 'Inventory',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 12,
        ])->id;

        Menu::create([
            'name' => 'Fuel Consumption',
            'icon' => null,
            'parent_id' => null,
            'route' => 'fuel',
            'order' => 17,
        ]);

        Menu::create([
            'name' => 'Monitoring',
            'icon' => null,
            'parent_id' => null,
            'route' => 'monitoring',
            'order' => 18,
        ]);

        Menu::insert([
            // ACL
            [
                'name' => 'Roles',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => 'role',
                'order' => 2,
            ],
            [
                'name' => 'Permissions',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => 'permision',
                'order' => 3,
            ],
            [
                'name' => 'Users',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => 'user',
                'order' => 4,
            ],
            [
                'name' => 'Menu',
                'icon' => null,
                'parent_id' => $parent1Id,
                'route' => 'menu',
                'order' => 5,
            ],
            // MASTER DATA
            [
                'name' => 'Asset',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'asset',
                'order' => 7,
            ],
            [
                'name' => 'Management Project',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'management-project',
                'order' => 8,
            ],
            // INVENTORI
            [
                'name' => 'Gudang',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => 'werehouse',
                'order' => 13,
            ],
            [
                'name' => 'Kategori',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => 'category-item',
                'order' => 14,
            ],
            [
                'name' => 'Barang',
                'icon' => null,
                'parent_id' => $parent3Id,
                'route' => 'item',
                'order' => 15,
            ],
        ]);

        $menu_report = Menu::create([
            'name' => 'Report',
            'icon' => null,
            'parent_id' => null,
            'route' => null,
            'order' => 13,
        ])->id;

        Menu::create([
            'name' => 'Fuel Consumtion',
            'icon' => null,
            'parent_id' => $menu_report,
            'route' => 'report-fuel',
            'order' => 1,
        ]);
        Menu::create([
            'name' => 'Asset Project',
            'icon' => null,
            'parent_id' => $menu_report,
            'route' => 'report-asset',
            'order' => 2,
        ]);
    }
}
