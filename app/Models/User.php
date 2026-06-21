<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmailNotification;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
            'password'          => 'hashed',
        ];
    }

    // ─── JWT Interface ────────────────────────────────────────────────────────

    /** Return the identifier that will be stored in the JWT subject claim. */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /** Return custom claims to be added to the JWT payload. */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function createdTenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'created_by');
    }

    public function tenderAnnouncements(): HasMany
    {
        return $this->hasMany(TenderAnnouncement::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TenderHistory::class, 'actor_id');
    }

    // Buat nge-override fungsi bawaan Laravel pas lupa password, biar nembak pake notifikasi custom (kirim token doang)
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    // Override fungsi verifikasi email biar pake notifikasi yang udah di-queue
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmailNotification);
    }
}
