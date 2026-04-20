<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('cascade');
            $table->unsignedBigInteger('brand_id')->nullable();

            // Basic product info
            $table->string('product_code')->unique();
            $table->string('barcode_code')->unique();
            $table->string('hsn_code')->nullable();
            $table->string('name');
            $table->string('slug')->unique();

            // Sales & pricing
            $table->string('unit')->nullable(); // PCS, KG, BOX, etc.
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('saleing_price', 10, 2)->nullable();
            $table->decimal('gst_percentage', 5, 2)->default(0);

            // Stock
            $table->integer('opening_stock')->default(0);
            $table->integer('low_stock_alert')->default(0);

            // Type
            $table->string('product_type')->default('simple');
            // simple | variant | service

            // Meta
            $table->boolean('status')->default(1);
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
