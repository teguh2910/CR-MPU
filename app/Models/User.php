<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_PURCHASING = 'purchasing';
    const ROLE_MANAGER = 'manager';

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

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Any authenticated user can access for now, logic is handled by policies
    }

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_PURCHASING => 'Purchasing',
            self::ROLE_MANAGER => 'Manager',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPurchasing(): bool
    {
        return $this->role === self::ROLE_PURCHASING;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }
}
