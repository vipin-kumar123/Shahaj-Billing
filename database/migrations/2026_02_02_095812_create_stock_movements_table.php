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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();

            $table->enum('type', ['increase', 'decrease']);
            $table->decimal('quantity', 10, 2)->default(0);

            $table->timestamps();

            // Optional Foreign Keys
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('set null');
            // $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
