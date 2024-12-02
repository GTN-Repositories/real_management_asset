<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CategoryItem extends Model
{
    protected $guarded = ['id'];
    protected $table = 'category_items';

    public function getIdAttribute()
    {
        return Crypt::encrypt($this->attributes['id']);
    }

    public static function findByEncryptedId($encryptedId)
    {
        $decryptedId = Crypt::decrypt($encryptedId);

        return self::findOrFail($decryptedId);
    }
}
