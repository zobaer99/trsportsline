<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTableAddon extends Model
{
    protected $fillable = ['group_id', 'category', 'label', 'amount', 'sort_order'];
}
