<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'phone', 'email', 'district', 'address', 'password', 'guest_access_token', 'is_guest'];
    protected $hidden = ['password', 'remember_token', 'guest_access_token'];
    protected $casts = ['is_guest' => 'boolean', 'password' => 'hashed'];

    public function orders(): HasMany { return $this->hasMany(Order::class); }
}
