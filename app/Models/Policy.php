<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'no_blanko',
        'no_policy',
        'consignee',
        'no_bl',
        'shipping_carrier',
        'insured_value',
        'currency',
        'status',
        'premium_price',
        'verification_reason',
        'payment_proof',
    ];

    /**
     * Get the user that owns the policy.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
