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
        Schema::create('party_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('party_id'); // customer/supplier

            $table->string('reference_type')->nullable();
            // purchase, sale, purchase_return, sale_return

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->enum('type', ['debit', 'credit']);
            // debit = paisa diya
            // credit = paisa mila

            $table->decimal('amount', 15, 2);

            $table->string('payment_method')->nullable();
            $table->date('payment_date')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_transactions');
    }
};
