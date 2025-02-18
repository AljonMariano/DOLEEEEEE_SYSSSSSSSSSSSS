<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique(); // Format: YYYYMMXXX
            $table->date('date');
            $table->time('time');
            $table->string('origin');
            $table->string('received_by');
            $table->string('subject');
            $table->string('file_case_no');
            $table->string('forward_to');
            $table->string('current_location')->nullable();
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('completely_signed')->default(false);
            $table->date('last_action_date')->nullable();
            $table->time('last_action_time')->nullable();
            $table->timestamps();
        });

        Schema::create('order_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('from_department');
            $table->string('to_department');
            $table->dateTime('routed_at');
            $table->dateTime('received_at')->nullable();
            $table->string('status'); // pending, received, completed
            $table->text('notes')->nullable();
            $table->string('action_taken')->nullable();
            $table->string('action_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_routes');
        Schema::dropIfExists('orders');
    }
}; 