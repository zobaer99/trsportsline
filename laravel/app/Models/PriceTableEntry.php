<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTableEntry extends Model
{
    protected $fillable = ['group_id', 'fabric', 'moq', 'price', 'offer_price', 'sort_order'];
}
