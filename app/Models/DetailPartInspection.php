<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DetailPartInspection extends Model
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

    public function inspectionQuestion()
    {
        return $this->belongsTo(InspectionQuestion::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
