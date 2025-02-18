<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_request_id')->constrained()->onDelete('cascade');
            $table->string('ors_no');
            $table->string('po_no');
            $table->date('po_date');
            $table->string('dv_no')->nullable();
            $table->date('dv_date')->nullable();
            $table->string('payee');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamp('date_processed')->nullable();
            $table->timestamps();

            // Add unique constraints
            $table->unique('budget_request_id');
            $table->unique('po_no');
            $table->unique('ors_no');
            
            // Add composite index for DV number and related fields
            $table->index(['dv_no', 'payee', 'amount'], 'related_dv_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_requests');
    }
}; 