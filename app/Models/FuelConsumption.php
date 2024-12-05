<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class FuelConsumption extends Model
{
    //
    protected $guarded = ['id'];
    protected $table = 'fuel_consumptions';

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
     * Get the management_project that owns the FuelConsumption
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getAssetsAttribute()
    {
        return Asset::where('id', $this->asset_id)->first();
    }

    public function management_project(): BelongsTo
    {
        return $this->belongsTo(ManagementProject::class, 'management_project_id', 'id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id', 'id');
    }

    public function loadsheets()
    {
        return $this->hasMany(Loadsheet::class, 'asset_id', 'asset_id');
    }

    public function loadsheetsManagement()
    {
        return $this->hasMany(Loadsheet::class, 'management_project_id', 'management_project_id');
    }
}
