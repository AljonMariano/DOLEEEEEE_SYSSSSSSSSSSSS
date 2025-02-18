<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_no')->unique();
            $table->date('pr_date');
            $table->decimal('amount', 10, 2);
            $table->text('purpose');
            $table->string('status')->default('pending');
            $table->timestamp('date_processed')->useCurrent();
            $table->timestamps();
        });

        Schema::create('purchase_request_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('purchase_request_routes');
        Schema::dropIfExists('purchase_requests');
    }
}; 