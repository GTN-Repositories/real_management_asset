<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic', 'id');
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

    public function fuel_consumptions(): HasMany
    {
        return $this->hasMany(FuelConsumption::class, 'asset_id', 'id');
    }

    public static function getStatusTypes()
    {
        return [
            'operational' => ['Idle', 'StandBy', 'UnderMaintenance', 'Active'],
            'maintenance' => ['OnHold', 'Finish', 'Scheduled', 'InProgress'],
            'condition' => ['Damaged', 'Fair', 'NeedsRepair', 'Good']
        ];
    }

    public function asset_category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category', 'id');
    }

    public function asset_manager(): BelongsTo
    {
        return $this->belongsTo(AssetManager::class, 'manager', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'assets_location', 'id');
    }

    public function pics(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'pic', 'id');
    }
}
