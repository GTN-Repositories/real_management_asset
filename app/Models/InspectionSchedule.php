<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class InspectionSchedule extends Model
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

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
    public function managementProject()
    {
        return $this->belongsTo(ManagementProject::class, 'management_project_id', 'id');
    }
    public function assetKanibal()
    {
        return $this->belongsTo(Asset::class, 'asset_kanibal_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function werehouse()
    {
        return $this->belongsTo(Werehouse::class, 'werehouse_id', 'id');
    }

    public function comment()
    {
        return $this->hasMany(InspectionComment::class, 'inspection_schedule_id', 'id');
    }
}
