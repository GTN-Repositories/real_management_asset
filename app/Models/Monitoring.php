<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Monitoring extends Model
{
    //
    protected $guarded = ['id'];

    public function getIdAttribute()
    {
        return Crypt::encrypt($this->attributes['id']);
    }

    public static function findByEncryptedId($encryptedId)
    {
        $decryptedId = Crypt::decrypt($encryptedId);

        return self::findOrFail($decryptedId);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
}
