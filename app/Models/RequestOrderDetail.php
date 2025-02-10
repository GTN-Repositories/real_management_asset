<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestOrderDetail extends Model
{
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

    public function warehouse()
    {
        return $this->belongsTo(Werehouse::class, 'warehouse_id', 'id');
    }
}
