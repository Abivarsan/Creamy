<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Check if user is an Owner.
     */
    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    /**
     * Check if user is a Manager.
     */
    public function isManager(): bool
    {
        return $this->role === UserRole::MANAGER;
    }

    /**
     * Check if user is a Cashier.
     */
    public function isCashier(): bool
    {
        return $this->role === UserRole::CASHIER;
    }

    /**
     * Check if user has any of the specified roles.
     *
     * @param array<UserRole|string>|UserRole|string $roles
     */
    public function hasRole(array|UserRole|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        
        foreach ($roles as $role) {
            $value = $role instanceof UserRole ? $role->value : $role;
            if ($this->role->value === $value) {
                return true;
            }
        }

        return false;
    }
}
