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
        Schema::table('sale_returns', function (Blueprint $table) {
            // refunded_amount: total refunded till now
            $table->decimal('refunded_amount', 10, 2)->default(0)->after('total_return_amount');

            // refund_due: how much refund pending
            $table->decimal('refund_due', 10, 2)->default(0)->after('refunded_amount');

            // refund_status: NOT PAID / PARTIAL / REFUNDED
            $table->string('refund_status')->default('NOT PAID')->after('refund_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn([
                'refunded_amount',
                'refund_due',
                'refund_status',
            ]);
        });
    }
};
