<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

use function PHPUnit\Framework\returnSelf;

class PurchaseOrderDetail extends Model
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

    public function warehouse()
    {
        return $this->belongsTo(Werehouse::class, 'warehouse_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function requestOrderDetail()
    {
        return $this->belongsTo(RequestOrderDetail::class, 'request_order_detail_id', 'id');
    }
}
