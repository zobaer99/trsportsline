<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceTableGroup extends Model
{
    protected $fillable = ['name', 'slug', 'currency', 'note', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function entries(): HasMany { return $this->hasMany(PriceTableEntry::class, 'group_id')->orderBy('sort_order'); }
    public function addons(): HasMany { return $this->hasMany(PriceTableAddon::class, 'group_id')->orderBy('category')->orderBy('sort_order'); }
}
