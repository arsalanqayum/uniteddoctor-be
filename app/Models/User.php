<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\HasApiTokens;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Cashier\Billable;
use App\Notifications\CustomVerifyEmail;
use App\Events\UserStatusUpdated;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements LaratrustUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions, Billable;
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }
    // public function sendPasswordResetNotification($token)
    // {
    //     $this->notify(new ResetPasswordNotification($token));
    // }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
        'city',
        'lat',
        'long',
        'avatar',
        'gender'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $appends = ['average_rating', 'reviews_count'];
    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();

        broadcast(new UserStatusUpdated($this->id, $status));
    }
    /** Relationships */
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
    public function isSubscribedToAnyPlan()
    {
        return $this->subscriptions->where('stripe_status', 'active')->isNotEmpty();
    }

    /** Accessor & Mutator */

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => bcrypt($value),
        );
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return config('app.url').Storage::url($value);
    }

    public function doctorSpecialities()
    {
        return $this->hasMany(DoctorSpeciality::class);
    }

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
    public function education(){
        return $this->hasMany(Education::class,'user_id','id');
    }
    public function expereince(){
        return $this->hasMany(Eperiance::class,'user_id','id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'doctor_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating');
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
