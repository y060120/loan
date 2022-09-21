<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\Auth\Entities\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    // test email duplication
    public function test_email_duplication(){
        $userOne = User::make([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin@1234'),
            'role'  => 'admin'
        ]);

        $userTwo = User::make([
            'name' => 'user',
            'email' => 'user@admin.com',
            'password' => Hash::make('abcd@1234'),
            'role'  => 'user'  
        ]);

        $this->assertTrue($userOne->email != $userTwo->email);
    }
    // test sample user registration
    public function test_user_registration(){
        $uniqueEmail = uniqid().'@gmail.com';
        $this->postJson('api/auth/userCreate',[
            'name' => 'TestUser',
            'email' => $uniqueEmail,
            'password' => Hash::make('admin@1234'),
            'role'  => 'user'
        ])
        ->assertStatus(200);
    }
    // test login
    public function test_user_login(){
        $this->postJson('api/auth/dologin',[            
            'email' => 'admin@admin.com',
            'password' => 'admin@1234'            
        ])
        ->assertStatus(200);
    }
}
