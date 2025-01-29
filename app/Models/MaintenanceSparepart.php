<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSparepart extends Model
{
    protected $guarded = ['id'];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
