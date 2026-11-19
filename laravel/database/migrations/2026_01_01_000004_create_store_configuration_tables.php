<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { public function up(): void {
    Schema::create('store_settings', function (Blueprint $table) { $table->id(); $table->string('key', 191)->unique(); $table->longText('value')->nullable(); $table->boolean('is_secret')->default(false); $table->timestamps(); });
    Schema::create('hero_banners', function (Blueprint $table) { $table->id(); $table->string('title'); $table->text('subtitle')->nullable(); $table->string('desktop_image'); $table->string('mobile_image')->nullable(); $table->string('button_label')->nullable(); $table->string('button_url')->nullable(); $table->unsignedSmallInteger('sort_order')->default(0); $table->boolean('is_active')->default(true); $table->timestamps(); });
    Schema::create('coupons', function (Blueprint $table) { $table->id(); $table->string('code', 191)->unique(); $table->string('type')->default('fixed'); $table->unsignedInteger('value'); $table->unsignedInteger('min_order')->default(0); $table->timestamp('starts_at')->nullable(); $table->timestamp('expires_at')->nullable(); $table->unsignedInteger('usage_limit')->nullable(); $table->unsignedInteger('usage_count')->default(0); $table->boolean('is_active')->default(true); $table->timestamps(); });
    Schema::create('delivery_zones', function (Blueprint $table) { $table->id(); $table->string('name', 191)->unique(); $table->unsignedInteger('charge'); $table->boolean('is_active')->default(true); $table->timestamps(); });
} public function down(): void { Schema::dropIfExists('delivery_zones'); Schema::dropIfExists('coupons'); Schema::dropIfExists('hero_banners'); Schema::dropIfExists('store_settings'); } };
