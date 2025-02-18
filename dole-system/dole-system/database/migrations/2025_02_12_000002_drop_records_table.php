<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('records');
    }

    public function down()
    {
        // We won't recreate the table in down() since we're consolidating to documents table
    }
}; 