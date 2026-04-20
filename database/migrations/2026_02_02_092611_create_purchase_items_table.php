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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');

            $table->decimal('unit_cost', 15, 2)->default(0);  // cost without GST
            $table->decimal('quantity', 15, 3)->default(1);

            $table->decimal('discount', 15, 2)->default(0);
            $table->enum('discount_type', ['flat', 'percent'])->nullable();

            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 15, 2)->default(0);

            $table->decimal('total', 15, 2)->default(0);

            $table->date('expiry_date')->nullable();
            $table->string('batch_no')->nullable();

            $table->timestamps();

            // Optional FKs
            // $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
