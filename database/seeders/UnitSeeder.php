<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'police_number' => 'B 1234 XYZ',
                'old_police_number' => 'B 4321 XYZ',
                'frame_number' => 'JH4DB8550RS123456',
                'merk' => 'Honda',
                'type_vehicle' => 'Sedan',
                'type' => 'Civic Type R',
                'year' => 2021,
                'color' => 'Championship White',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'D 5678 ABC',
                'old_police_number' => 'D 8765 ABC',
                'frame_number' => 'JT2AE86C2J1234567',
                'merk' => 'Toyota',
                'type_vehicle' => 'Coupe',
                'type' => 'AE86 Trueno',
                'year' => 1986,
                'color' => 'Panda',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'A 3456 DEF',
                'old_police_number' => 'A 6543 DEF',
                'frame_number' => 'JN1CZ24H8MX567891',
                'merk' => 'Nissan',
                'type_vehicle' => 'Coupe',
                'type' => 'Fairlady Z',
                'year' => 1999,
                'color' => 'Silver',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'B 9876 GHI',
                'old_police_number' => 'B 6789 GHI',
                'frame_number' => 'JF1GD29672G123456',
                'merk' => 'Subaru',
                'type_vehicle' => 'Sedan',
                'type' => 'Impreza WRX STI',
                'year' => 2004,
                'color' => 'World Rally Blue',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'F 2345 JKL',
                'old_police_number' => 'F 5432 JKL',
                'frame_number' => 'JCE12345678965432',
                'merk' => 'Mazda',
                'type_vehicle' => 'Coupe',
                'type' => 'RX-7',
                'year' => 1995,
                'color' => 'Red',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'G 8765 MNO',
                'old_police_number' => 'G 5678 MNO',
                'frame_number' => 'JD3CZ18G123456789',
                'merk' => 'Mitsubishi',
                'type_vehicle' => 'Sedan',
                'type' => 'Lancer Evolution IX',
                'year' => 2007,
                'color' => 'Yellow',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'H 4567 PQR',
                'old_police_number' => 'H 7654 PQR',
                'frame_number' => 'JHMCG6684YC012345',
                'merk' => 'Honda',
                'type_vehicle' => 'Sedan',
                'type' => 'Accord',
                'year' => 2010,
                'color' => 'Black',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'B 5432 STU',
                'old_police_number' => 'B 2345 STU',
                'frame_number' => 'JH4DB8590RS789123',
                'merk' => 'Honda',
                'type_vehicle' => 'Coupe',
                'type' => 'NSX',
                'year' => 2002,
                'color' => 'Red',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'D 7890 VWX',
                'old_police_number' => 'D 0987 VWX',
                'frame_number' => 'JT2AE86C3K6789123',
                'merk' => 'Toyota',
                'type_vehicle' => 'Coupe',
                'type' => 'Supra MK4',
                'year' => 1998,
                'color' => 'Black',
                'physical_status' => 'Good',
            ],
            [
                'police_number' => 'A 6789 YZA',
                'old_police_number' => 'A 9876 YZA',
                'frame_number' => 'JN1RZ26H5RX456789',
                'merk' => 'Nissan',
                'type_vehicle' => 'Coupe',
                'type' => 'Skyline GT-R R34',
                'year' => 2001,
                'color' => 'Bayside Blue',
                'physical_status' => 'Good',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
