<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Maintenance extends Model
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

    public function inspection_schedule()
    {
        return $this->belongsTo(InspectionSchedule::class, 'inspection_schedule_id', 'id');
    }

    public function getEmployeeIdAttribute($value)
    {
        return implode(', ', json_decode($value));
    }

    public function maintenanceSparepart()
    {
        return $this->hasMany(MaintenanceSparepart::class, 'maintenance_id', 'id');
    }
}
