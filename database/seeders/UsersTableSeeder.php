<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(array (
            0 => 
            array (
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin@1234'),
                'role'  => 'admin'
            ),
            1 => 
            array (
                'name' => 'user',
                'email' => 'user@gmail.com',
                'password' => Hash::make('abcd@1234'),
                'role'  => 'user'            
                )
            ));
    }
}

