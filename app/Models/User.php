<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_hash',
        'password',
        'role',
        'department_id',
        'is_active',
        'mfa_method',
        'mfa_secret',
        'mfa_verified_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
        'email_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'mfa_verified_at'   => 'datetime',
        'approved_at'       => 'datetime',
        'rejected_at'       => 'datetime',
        'mfa_secret'        => 'encrypted',
        'email'             => 'encrypted',
    ];

    // -------------------------------------------------------------------------
    // Auto-compute email_hash on save
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty('email')) {
                $plainEmail = $user->email; // decrypted by cast
                $user->email_hash = hash('sha256', strtolower($plainEmail));
            }
        });
    }

    // -------------------------------------------------------------------------
    // Email masking accessor
    // -------------------------------------------------------------------------

    public function getMaskedEmailAttribute(): string
    {
        $email = $this->email;
        if (!$email || !str_contains($email, '@')) {
            return '***@***.***';
        }

        [$local, $domain] = explode('@', $email);
        $len = strlen($local);

        if ($len <= 2) {
            $masked = $local[0] . str_repeat('*', max($len - 1, 1));
        } elseif ($len <= 4) {
            $masked = $local[0] . str_repeat('*', $len - 2) . $local[$len - 1];
        } else {
            $masked = substr($local, 0, 2) . str_repeat('*', $len - 3) . substr($local, -1);
        }

        return $masked . '@' . $domain;
    }

    // -------------------------------------------------------------------------
    // Static helper: find user by email using hash
    // -------------------------------------------------------------------------

    public static function findByEmail(string $email): ?self
    {
        $hash = hash('sha256', strtolower(trim($email)));
        return static::where('email_hash', $hash)->first();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // -------------------------------------------------------------------------
    // Role helpers
    // -------------------------------------------------------------------------

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isInventory()
    {
        return $this->role === 'inventory';
    }

    public function isSales()
    {
        return $this->role === 'sales';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isPendingApproval(): bool
    {
        return $this->mfa_verified_at !== null
            && !$this->is_active
            && $this->rejected_at === null;
    }

    public function mfaCodes()
    {
        return $this->hasMany(MfaCode::class);
    }
}
