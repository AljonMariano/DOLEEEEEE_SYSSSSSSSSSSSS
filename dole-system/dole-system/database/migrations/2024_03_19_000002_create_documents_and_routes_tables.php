<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_id')->unique(); // Format: YYYYMMXXX
            $table->date('date_received');
            $table->time('time_received');
            $table->string('origin'); // Where the document came from
            $table->string('subject');
            $table->string('forward_to'); // Initial department to forward to
            $table->string('current_location')->nullable(); // Current department location
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->boolean('completely_signed')->default(false);
            $table->date('last_action_date')->nullable();
            $table->time('last_action_time')->nullable();
            $table->timestamps();
        });

        Schema::create('document_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('from_department');
            $table->string('to_department');
            $table->dateTime('routed_at');
            $table->dateTime('received_at')->nullable();
            $table->string('status'); // pending, received, completed
            $table->text('notes')->nullable();
            $table->string('action_taken')->nullable(); // What action was taken
            $table->string('action_by')->nullable(); // Who took the action
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_routes');
        Schema::dropIfExists('documents');
    }
}; 