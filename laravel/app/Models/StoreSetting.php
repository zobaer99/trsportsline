<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['key','value','is_secret'];
    protected $casts = ['is_secret' => 'boolean'];
}
