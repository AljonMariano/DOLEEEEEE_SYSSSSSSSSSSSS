<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        // First create the admin user with a temporary email
        DB::table('users')->insert([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@temp.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Then update to set email to NULL
        DB::table('users')
            ->where('username', 'admin')
            ->update(['email' => null]);
    }

    public function down()
    {
        DB::table('users')->where('username', 'admin')->delete();
    }
}; 