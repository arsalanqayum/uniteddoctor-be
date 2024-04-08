<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSpeciality extends Model
{
    use HasFactory;

    protected $fillable = ['specialization_id', 'user_id'];
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }
}
