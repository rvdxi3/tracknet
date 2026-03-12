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
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'mfa_verified_at'   => 'datetime',
        'approved_at'       => 'datetime',
        'rejected_at'       => 'datetime',
        'mfa_secret'        => 'encrypted',
    ];

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
        return $this->hasMany(Sale::class, 'user_id'); // sales person
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

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