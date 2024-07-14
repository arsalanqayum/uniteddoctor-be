<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DoctorAvailability extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id', 'date', 'duration', 'start_time', 'end_time', 'is_repeated', 'type', 'location', 'schedule_id', 'location_id',
        'location_address',
        'latitude',
        'longitude'
    ];

    public function getTimeslotsAttribute()
    {
        $timeslots = [];
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);
        // dd($this->duration);
        $timeString = $this->duration;
        // dd($timeString,$startTime);
        $time = Carbon::createFromFormat('H:i:s', $timeString);
        $totalMinutes = $time->hour * 60 + $time->minute;
        // dd($totalMinutes);
        // echo $minutes;
        while ($startTime->lt($endTime)) {
            $timeslots[] = [
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $startTime->addMinutes($totalMinutes)->format('H:i:s')
            ];
        }

        return $timeslots;
    }

    // Append the timeslots attribute to the model's array form
    protected $appends = ['timeslots'];
}
