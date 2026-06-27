<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'asal_sekolah',
        'no_wa',
        'status_verifikasi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Registrations
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Relasi langsung User ke Lomba yang diikutinya
     */
    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'registrations')
                    ->withPivot(['status_pendaftaran', 'status_pembayaran', 'nilai_cbt', 'is_winner', 'peringkat'])
                    ->withTimestamps();
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    // ==========================================
    // MEDIA LIBRARY CONFIGURATION
    // ==========================================

    /**
     * Register media collections dan conversions
     * Fungsi ini otomatis mengkompres gambar saat diupload
     */
    public function registerMediaCollections(): void
    {
        // Collection untuk foto profil
        $this->addMediaCollection('profile_picture')
             ->singleFile();
    }

    /**
     * Register media conversions (kompresi otomatis)
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Konversi untuk thumbnail (300x300, WebP, 70% quality)
        $this->addMediaConversion('thumb')
             ->width(300)
             ->height(300)
             ->quality(70)
             ->format('webp')
             ->nonQueued()
             ->performOnCollections('profile_picture');

        // Konversi untuk avatar kecil (100x100)
        $this->addMediaConversion('avatar')
             ->width(100)
             ->height(100)
             ->quality(60)
             ->format('webp')
             ->nonQueued()
             ->performOnCollections('profile_picture');

        // Konversi untuk tampilan medium (500x500)
        $this->addMediaConversion('medium')
             ->width(500)
             ->height(500)
             ->quality(75)
             ->format('webp')
             ->nonQueued()
             ->performOnCollections('profile_picture');
    }

    // ==========================================
    // HELPER METHODS (Opsional)
    // ==========================================

    /**
     * Mendapatkan URL foto profil
     */
    public function getProfilePictureUrl(?string $conversion = 'thumb'): ?string
    {
        $media = $this->getFirstMedia('profile_picture');
        if (!$media) {
            return null;
        }

        return $media->getUrl($conversion);
    }

    /**
     * Mendapatkan URL foto profil dengan fallback ke avatar default
     */
    public function getProfilePictureUrlOrDefault(?string $conversion = 'thumb'): string
    {
        $url = $this->getProfilePictureUrl($conversion);
        
        if ($url) {
            return $url;
        }

        // Fallback ke avatar default berdasarkan nama
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&size=300&background=6366f1&color=ffffff&bold=true";
    }

    /**
     * Cek apakah user memiliki foto profil
     */
    public function hasProfilePicture(): bool
    {
        return $this->hasMedia('profile_picture');
    }
}