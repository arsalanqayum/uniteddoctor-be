<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable=['doctor_availability_id','user_id','date','start_time','end_time','schedule_id','status','doctor_id'];

    public function doctor(){
        return $this->belongsTo(User::class,'doctor_id','id');
    }
    public function patient(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
