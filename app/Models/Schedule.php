<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'location',
        'consultation_fee',
        'user_id',
        'offer_label'
    ];

    public function available(){
        return $this->hasMany(DoctorAvailability::class);
    }
    public function doctor(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
