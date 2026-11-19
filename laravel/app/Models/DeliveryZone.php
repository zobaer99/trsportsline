<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model { protected $fillable = ['name','charge','is_active']; protected $casts = ['charge' => 'integer','is_active' => 'boolean']; }
