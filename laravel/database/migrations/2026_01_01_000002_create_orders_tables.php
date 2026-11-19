<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration { public function up(): void {
    Schema::create('orders', function (Blueprint $table) { $table->id(); $table->string('order_number', 40)->unique(); $table->string('customer_name'); $table->string('phone', 15)->index(); $table->string('district'); $table->text('address'); $table->string('delivery_zone'); $table->unsignedInteger('delivery_charge'); $table->string('payment_method'); $table->string('payment_reference')->nullable(); $table->string('payment_status', 32)->default('pending')->index(); $table->unsignedInteger('advance_payment')->default(0); $table->unsignedInteger('subtotal'); $table->unsignedInteger('total'); $table->string('status', 40)->default('order_placed')->index(); $table->string('courier')->nullable(); $table->string('courier_tracking_code')->nullable(); $table->string('courier_tracking_url')->nullable(); $table->text('notes')->nullable(); $table->timestamps(); });
    Schema::create('order_items', function (Blueprint $table) { $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $table->string('product_name'); $table->string('size', 8); $table->string('edition'); $table->string('custom_name', 14)->nullable(); $table->string('custom_number', 2)->nullable(); $table->json('patches')->nullable(); $table->unsignedSmallInteger('quantity'); $table->unsignedInteger('unit_price'); $table->unsignedInteger('line_total'); $table->timestamps(); });
} public function down(): void { Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); } };
