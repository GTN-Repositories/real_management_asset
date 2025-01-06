<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\Notification;
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

    public static function notification($limit = 10)
    {
        $data = Notification::where('read', 0)->orderBy('id', 'desc')->limit($limit)->get();

        foreach ($data as $notification) {
            $notification->body = self::getBodyContent($notification->body);
        }

        return $data;
    }

    public static function getBodyContent(string $html): string
    {
        $dom = new \DOMDocument();

        // Suppress warnings caused by invalid HTML
        libxml_use_internal_errors(true);

        // Load the HTML string into DOMDocument
        $dom->loadHTML($html);

        // Reset error handling
        libxml_clear_errors();

        // Remove <h1> tags from the document
        $h1Tags = $dom->getElementsByTagName('h1');
        while ($h1Tags->length > 0) {
            $h1Tags->item(0)->parentNode->removeChild($h1Tags->item(0));
        }

        // Extract the content inside the <body> tag
        $body = $dom->getElementsByTagName('body')->item(0);

        // Return the inner HTML of the body tag or empty string if not found
        return $body ? $dom->saveHTML($body) : '';
    }

}
