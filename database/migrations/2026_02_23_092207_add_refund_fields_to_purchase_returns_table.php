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
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('return_date');
            $table->decimal('refund_due', 10, 2)->default(0)->after('refunded_amount');
            $table->string('refund_status')->default('UNPAID')->after('refund_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn(['refunded_amount', 'refund_due', 'refund_status']);
        });
    }
};
