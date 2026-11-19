<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { public function up(): void { Schema::create('customer_reviews', function (Blueprint $table) { $table->id(); $table->string('customer_name'); $table->string('district'); $table->text('review'); $table->string('photo_url')->nullable(); $table->unsignedTinyInteger('rating')->default(5); $table->boolean('is_verified')->default(false); $table->boolean('is_published')->default(false)->index(); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('customer_reviews'); } };
