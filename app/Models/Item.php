<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Item extends Model
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

    public function category()
    {
        return $this->belongsTo(CategoryItem::class, 'category_id', 'id');
    }

    public function oum()
    {
        return $this->belongsTo(Oum::class, 'oum_id', 'id');
    }

    public function detailPartInspections()
    {
        return $this->hasMany(DetailPartInspection::class);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', 'minimum_stock');
    }
}
