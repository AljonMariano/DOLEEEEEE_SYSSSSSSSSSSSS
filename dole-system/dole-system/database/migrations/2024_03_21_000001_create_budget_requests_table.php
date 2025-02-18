<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budget_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->string('pr_no');
            $table->date('pr_date');
            $table->string('ors_no')->nullable();
            $table->date('ors_date')->nullable();
            $table->string('payee')->nullable();
            $table->text('purpose');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamp('date_processed')->nullable();
            $table->timestamps();

            // Add unique constraint to ensure one budget request per purchase request
            $table->unique('purchase_request_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('budget_requests');
    }
}; 