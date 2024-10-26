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
            'name' => 'Inspeksi',
            'icon' => null,
            'parent_id' => null,
            'route' => 'inspection-schedule',
            'order' => 17,
        ]);

        Menu::insert([
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
            [
                'name' => 'Site',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'site',
                'order' => 7,
            ],
            [
                'name' => 'Kontrak',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'kontrak',
                'order' => 8,
            ],
            [
                'name' => 'Pelanggan',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'pelanggan',
                'order' => 9,
            ],
            [
                'name' => 'Supplier',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'supplier',
                'order' => 10,
            ],
            [
                'name' => 'Kendaraan / Unit',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'unit',
                'order' => 11,
            ],
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
            [
                'name' => 'Pertanyaan Inspeksi',
                'icon' => null,
                'parent_id' => $parent2Id,
                'route' => 'form',
                'order' => 16,
            ],
            
        ]);
    }
}
