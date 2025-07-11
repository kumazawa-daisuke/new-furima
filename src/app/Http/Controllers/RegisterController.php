<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // 会員登録フォーム表示
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // 会員登録処理
    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();
        // ユーザー作成
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // ここで認証メールを自動送信！
        $user->sendEmailVerificationNotification();

        // 認証案内ページにリダイレクト
        return redirect()->route('verification.notice');
    }
}