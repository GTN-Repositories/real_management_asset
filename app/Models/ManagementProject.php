<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ManagementProject extends Model
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

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function getAssetsAttribute()
    {
        return Asset::whereIn('id', $this->asset_id)->get();
    }

    protected $casts = [
        'asset_id' => 'array',
        'employee_id' => 'array',
    ];
    
    public function monitorings()
    {
        return $this->hasManyThrough(
            Monitoring::class,
            Asset::class,
            'id', // Foreign key on assets table
            'asset_id', // Foreign key on monitorings table
            'asset_id', // Local key on management_projects table
            'id' // Local key on assets table
        );
    }

    public function pettyCash()
    {
        return $this->hasMany(PettyCash::class);
    }
}
