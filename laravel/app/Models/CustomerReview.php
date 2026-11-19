<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReview extends Model { protected $fillable = ['customer_name','district','review','photo_url','rating','is_verified','is_published']; protected $casts = ['rating' => 'integer','is_verified' => 'boolean','is_published' => 'boolean']; }
