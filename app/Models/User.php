<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password'
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

    public function subscribed()
    {
        return $this->hasOne(Subscription::class)->whereActive(1)->where('ends_at', '>', now())->latest();
    }
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->whereActive(1)->latest();
    }

    public function editedImagesCount()
    {
        $edited_images_count =  $this->hasMany(EditedImageCount::class)->whereSubscriptionId($this->subscribed?->id);

        if ($this->subscribed->plan->period == 'year')
            $edited_images_count = $edited_images_count->whereMonth('created_at', now()->month);

        return $edited_images_count->count();
    }
}
