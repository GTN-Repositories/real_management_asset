<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Reminder untuk Pengingat Asuransi
            [
                'group' => 'reminder',
                'key' => 'insurance_reminder_period',
                'value' => '30',
                'long_value' => 'Reminder asuransi dikirim 30 hari sebelum masa berlaku habis.',
                'status' => 'active'
            ],
            // Reminder untuk Pajak
            [
                'group' => 'reminder',
                'key' => 'tax_reminder_period',
                'value' => '30',
                'long_value' => 'Reminder pajak dikirim 30 hari sebelum masa berlaku habis.',
                'status' => 'active'
            ],
            // Reminder untuk Kontrak
            [
                'group' => 'reminder',
                'key' => 'contract_reminder_period',
                'value' => '30',
                'long_value' => 'Reminder kontrak dikirim 30 hari sebelum masa berlaku habis.',
                'status' => 'active'
            ],
            // Reminder untuk Petty Cash
            [
                'group' => 'reminder',
                'key' => 'petty_cash_reminder_period',
                'value' => '7',
                'long_value' => 'Reminder untuk approval petty cash dikirim 7 hari sebelum batas waktu.',
                'status' => 'active'
            ],
            // Reminder untuk Stok Sparepart Rendah
            [
                'group' => 'reminder',
                'key' => 'low_stock_reminder_period',
                'value' => null,
                'long_value' => 'Reminder dikirim segera setelah stok sparepart rendah.',
                'status' => 'active'
            ],
            // Reminder untuk Approval Sparepart
            [
                'group' => 'reminder',
                'key' => 'approval_reminder_sparepart_period',
                'value' => 'pending',
                'long_value' => 'Reminder approval sparepart dikirim 2 hari setelah status pending.',
                'status' => 'active'
            ],
            // Reminder untuk Penambahan Bahan Bakar
            [
                'group' => 'reminder',
                'key' => 'fuel_stock_addition_period',
                'value' => '0',
                'long_value' => 'Reminder dikirim segera setelah bahan bakar ditambahkan.',
                'status' => 'active'
            ],
            [
                'group' => 'reminder',
                'key' => 'reminder_change_status_asset',
                'value' => null,
                'long_value' => 'Reminder dikirim setelah status asset diubah.',
                'status' => 'inactive'
            ]
        ];

        GeneralSetting::insert($settings);
    }
}
