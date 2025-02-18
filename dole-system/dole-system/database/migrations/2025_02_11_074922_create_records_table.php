<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('doc_id')->unique();
            $table->string('origin_source');
            $table->dateTime('time');
            $table->string('subject');
            $table->string('forward_to');
            $table->boolean('tssd')->default(false);
            $table->boolean('imsd')->default(false);
            $table->boolean('planning_officer')->default(false);
            $table->boolean('hrmo')->default(false);
            $table->boolean('supply')->default(false);
            $table->boolean('acctg')->default(false);
            $table->boolean('malsu')->default(false);
            $table->boolean('oard')->default(false);
            $table->boolean('ord')->default(false);
            $table->boolean('is_completely_signed')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('records');
    }
}; 