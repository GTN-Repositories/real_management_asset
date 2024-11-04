<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Asset extends Model
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

    /**
     * Get all of the management_project for the Asset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // Asset.php
    public function managements(): HasMany
    {
        return $this->hasMany(ManagementProject::class, 'asset_id', 'id');
    }

    public static function getStatusTypes()
    {
        return [
            'operational' => ['Idle', 'StandBy', 'UnderMaintenance', 'Active'],
            'maintenance' => ['OnHold', 'Finish', 'Scheduled', 'InProgress'],
            'condition' => ['Damaged', 'Fair', 'NeedsRepair', 'Good']
        ];
    }
}
