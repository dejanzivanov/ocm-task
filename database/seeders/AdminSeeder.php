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
            'username' => config('admin.username'),
            'email' => config('admin.email'),
            'password' => Hash::make(config('admin.password')),        
            'is_admin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

