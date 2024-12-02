<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Ipb extends Model
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
     * Get the management_project that owns the Ipb
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function management_project(): BelongsTo
    {
        return $this->belongsTo(ManagementProject::class, 'management_project_id', 'id');
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
