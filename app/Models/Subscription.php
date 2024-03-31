<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_id',
        'payment_subscription_id',
        'payment_plan_id',
        'payment_method',
        'title',
        'price',
        'currency',
        'images_count',
        'auto_renewal',
        'ends_at'
    ];
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
