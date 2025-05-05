<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'ocm_administrator',
            'username' => env('ADMIN_USERNAME', ''),
            'email' => env('ADMIN_EMAIL', ''),
            'password' => Hash::make(env('ADMIN_PASSWORD', '')),         
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

