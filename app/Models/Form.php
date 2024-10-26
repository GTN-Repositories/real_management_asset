<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Form extends Model
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

    public function categoryFrom()
    {
        return $this->belongsTo(CategoryForm::class, 'category_form_id', 'id');
    }
    
    public function inspectionQuestions()
    {
        return $this->hasMany(InspectionQuestion::class);
    }
}
