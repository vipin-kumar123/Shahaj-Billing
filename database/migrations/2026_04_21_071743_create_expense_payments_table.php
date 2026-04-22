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
        Schema::create('expense_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();

            $table->date('payment_date');

            $table->decimal('amount', 12, 2);

            $table->string('payment_method')->nullable(); // cash, bank, upi
            $table->string('reference_no')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_payments');
    }
};
