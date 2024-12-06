<?php

namespace App\Helpers;

use App\Models\Menu;
use Illuminate\Support\Facades\Crypt;

class Helper
{
    public static function getMenu()
    {
        return Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
    }

    public static function encrypt($value)
    {
        return Crypt::encrypt($value);
    }

    public static function statusPettyCash($value)
    {
        if ($value == 1) {
            return 'Process';
        } else if ($value == 2) {
            return 'Approve';
        } else if ($value == 3) {
            return 'Reject';
        }
    }

    public static function bulan()
    {
        $data = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return $data;
    }
    public static function tahun()
    {
        // 10 TAHUN KEBELAKANG COLLECT TO ARRAY
        $data = collect(range(date('Y'), date('Y') - 10))->toArray();

        return $data;
    }
}
