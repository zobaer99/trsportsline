<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['customer_id','order_number','customer_name','phone','district','address','delivery_zone','delivery_charge','payment_method','payment_reference','payment_status','advance_payment','subtotal','total','status','courier','courier_tracking_code','courier_tracking_url','notes'];
    protected $casts = ['delivery_charge' => 'integer','advance_payment' => 'integer','subtotal' => 'integer','total' => 'integer'];
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
