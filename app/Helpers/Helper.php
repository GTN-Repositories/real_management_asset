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
}
