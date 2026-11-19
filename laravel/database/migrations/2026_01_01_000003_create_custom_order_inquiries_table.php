<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { public function up(): void { Schema::create('custom_order_inquiries', function (Blueprint $table) { $table->id(); $table->string('team_name'); $table->string('contact_name'); $table->string('phone', 15)->index(); $table->unsignedSmallInteger('quantity'); $table->string('fabric'); $table->string('district'); $table->text('notes')->nullable(); $table->string('design_file_path')->nullable(); $table->string('status', 40)->default('new')->index(); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('custom_order_inquiries'); } };
