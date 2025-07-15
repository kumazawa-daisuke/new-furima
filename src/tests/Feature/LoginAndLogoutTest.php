<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginAndLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレス未入力はバリデーションエラー()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_パスワード未入力はバリデーションエラー()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_未登録の情報ではログインできない()
    {
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'invalidpass',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_正しい情報でログインできる()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/'); // ログイン後のリダイレクト先に応じて変更
        $this->assertAuthenticatedAs($user);
    }

    public function test_ログアウトができる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
