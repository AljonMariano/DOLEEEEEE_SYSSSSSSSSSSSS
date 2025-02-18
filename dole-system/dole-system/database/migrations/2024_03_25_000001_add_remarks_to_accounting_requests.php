<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounting_requests', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('status');
            $table->decimal('tax', 30, 2)->nullable();
            $table->string('payment_type')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('payment_remarks')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounting_requests', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('tax');
            $table->dropColumn('payment_type');
            $table->dropColumn('reference_no');
            $table->dropColumn('payment_date');
            $table->dropColumn('payment_remarks');
        });
    }
}; 