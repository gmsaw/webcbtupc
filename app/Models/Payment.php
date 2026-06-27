<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'registration_id',
        'order_id',
        'amount',
        'status', 
        'payment_type', 
        'snap_token', 
        'paid_at'
        ];

    public function registration() { return $this->belongsTo(Registration::class); }
}
