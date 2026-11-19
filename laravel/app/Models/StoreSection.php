<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSection extends Model
{
    protected $fillable = ['page', 'section_key', 'title', 'subtitle', 'body', 'image_url', 'mobile_image_url', 'button_label', 'button_url', 'sort_order', 'is_active', 'settings'];
    protected $casts = ['is_active' => 'boolean', 'settings' => 'array'];
}
