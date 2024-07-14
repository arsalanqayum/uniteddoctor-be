<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eperiance extends Model
{
    use HasFactory;
    protected $fillable = ['description','employer','endDate','jobTitle','startDate','user_id'];
}
