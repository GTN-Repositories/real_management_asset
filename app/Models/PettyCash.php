<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PettyCash extends Model
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

    public function project()
    {
        return $this->belongsTo(ManagementProject::class, 'project_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function scopePendingRequests($query)
    {
        // Ambil data petty cash yang belum diproses dengan request_date mendekati batas waktu
        return $query->where('status', 1)
                     ->whereDate('date', '<=', now()->addDays(7)); // Reminder 7 hari sebelum deadline
    }
}
