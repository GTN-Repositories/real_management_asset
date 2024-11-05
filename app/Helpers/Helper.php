<?php
namespace App\Helpers;

use App\Models\Menu;

class Helper
{
    public static function getMenu()
    {
        return Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
    }
}

