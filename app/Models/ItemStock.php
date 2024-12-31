<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ItemStock extends Model
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'request_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'aproved_by', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Werehouse::class, 'warehouse_id', 'id');
    }
}
