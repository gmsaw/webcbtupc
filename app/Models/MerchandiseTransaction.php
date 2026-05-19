<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MerchandiseTransaction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'merchandise_id',
        'nominal',
        'metode_pembayaran',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class);
    }
}