<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 16)->unique();
            $table->string('email')->nullable();
            $table->string('district', 80)->nullable();
            $table->text('address')->nullable();
            $table->string('password')->nullable();
            $table->string('guest_access_token')->nullable();
            $table->boolean('is_guest')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::create('store_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 40);
            $table->string('section_key', 80);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('body')->nullable();
            $table->string('image_url')->nullable();
            $table->string('mobile_image_url')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['page', 'section_key']);
            $table->index(['page', 'is_active', 'sort_order']);
        });

        Schema::create('price_table_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->string('currency', 3)->default('BDT');
            $table->text('note')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('price_table_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('price_table_groups')->cascadeOnDelete();
            $table->string('fabric', 120);
            $table->unsignedSmallInteger('moq')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('offer_price')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('price_table_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('price_table_groups')->cascadeOnDelete();
            $table->string('category', 40)->default('addon');
            $table->string('label', 120);
            $table->unsignedInteger('amount')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_table_addons');
        Schema::dropIfExists('price_table_entries');
        Schema::dropIfExists('price_table_groups');
        Schema::dropIfExists('store_sections');
        Schema::table('orders', function (Blueprint $table) { $table->dropConstrainedForeignId('customer_id'); });
        Schema::dropIfExists('customers');
    }
};
