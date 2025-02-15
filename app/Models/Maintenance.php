<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Maintenance extends Model
{
    //
    protected $guarded = ['id'];
    protected $dates = ['finish_at', 'date'];

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

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function getEmployeeIdAttribute($value)
    {
        return implode(', ', json_decode($value));
    }

    public function maintenanceSparepart()
    {
        return $this->hasMany(MaintenanceSparepart::class, 'maintenance_id', 'id');
    }

    public function getStatusForDate(Carbon $date, $shift)
    {
        // Jika tanggal di luar rentang maintenance
        if ($date->lt($this->date)) {
            return 'Aktif';
        }

        // Jika maintenance sudah selesai
        if ($date->gte(Carbon::parse($this->finish_at))) {
            return 'Aktif';
        }

        // Hitung hari berlalu
        $daysElapsed = $date->diffInDays($this->date) + 1;

        // Tentukan shift
        $hour = $date->hour;
        $currentShift = ($hour >= 6 && $hour < 18) ? 'DS' : 'NS';

        // Jika shift tidak sesuai
        if ($currentShift !== $shift) {
            return 'Aktif';
        }

        // Tentukan status berdasarkan hari keberapa
        return match (true) {
            $daysElapsed === 2 => 'Sedang',
            $daysElapsed > 2 => 'Berat',
            default => 'Ringan',
        };
    }
}
