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
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'two_factor_secret',
        'two_factor_enabled',
        'last_login_ip',
        'last_login_at',
        'admin_permissions',
        'department',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
        'two_factor_secret',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'admin_permissions'    => 'array',
        ];
    }
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role,
        ];
    }
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
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /** Cek apakah user punya role admin (semua variant) */
    public function isAdminRole(): bool
    {
        return in_array($this->role, ['admin','super_admin','procurement_manager','evaluator','verifikator','auditor']);
    }

    /** Cek apakah user punya permission tertentu */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin' || $this->role === 'admin') return true;
        $perms = $this->admin_permissions ?? [];
        return in_array('*', $perms) || in_array($permission, $perms);
    }
}
