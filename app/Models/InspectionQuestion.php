<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class InspectionQuestion extends Model
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

    public function schedule()
    {
        return $this->belongsTo(InspectionSchedule::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function partDetails()
    {
        return $this->hasMany(DetailPartInspection::class);
    }    
}