<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOrderInquiry extends Model
{
    protected $fillable = ['team_name','contact_name','phone','quantity','fabric','district','notes','design_file_path','status'];
    protected $casts = ['quantity' => 'integer'];
}
