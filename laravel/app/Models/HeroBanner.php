<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model { protected $fillable = ['title','subtitle','desktop_image','mobile_image','button_label','button_url','sort_order','is_active']; protected $casts = ['is_active' => 'boolean']; }
