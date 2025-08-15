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
        'certificate_no',
        'date_of_issue',
        'vessel_reg',
        'sailing_date',
        'from',
        'to',
        'transhipment_at',
        'value_at',
        'interest_insured',
        'status',
        'premium_price',
        'verification_reason',
        'payment_proof',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user that owns the policy.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the user who created the policy.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the policy.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
