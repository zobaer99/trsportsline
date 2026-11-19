<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { public function up(): void { Schema::create('products', function (Blueprint $table) { $table->id(); $table->string('slug', 180)->unique(); $table->string('name'); $table->string('club')->nullable(); $table->string('category', 191)->index(); $table->text('description')->nullable(); $table->unsignedInteger('price'); $table->unsignedInteger('compare_at_price')->nullable(); $table->string('image_url')->nullable(); $table->string('badge')->nullable(); $table->string('edition')->default('Fan Edition'); $table->json('variants')->nullable(); $table->unsignedInteger('stock')->default(0); $table->decimal('rating_value', 2, 1)->nullable(); $table->unsignedInteger('review_count')->default(0); $table->boolean('is_active')->default(true)->index(); $table->string('meta_title')->nullable(); $table->text('meta_description')->nullable(); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('products'); } };
