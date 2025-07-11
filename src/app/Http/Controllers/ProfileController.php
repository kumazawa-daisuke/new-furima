<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // マイページ
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->input('tab', 'sell'); // デフォルトは'sell'

        if ($tab === 'buy') {
            // 購入した商品のリスト
            $items = $user->purchases()->with('item')->get()->pluck('item')->filter();
        } else {
            $items = $user->sellingItems()->with(['likes', 'comments'])->get();
        }

        return view('profile', [
            'user' => $user,
            'tab' => $tab,
            'items' => $items,
        ]);
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = \App\Models\User::with('address')->find(Auth::id());
        return view('profile_edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // ユーザー名更新
        $user->name = $request->input('name');

        // プロフィール画像アップロード
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（必要なら）
            if ($user->profile_image && \Storage::disk('public')->exists('profile/'.$user->profile_image)) {
                \Storage::disk('public')->delete('profile/'.$user->profile_image);
            }
            // 新しい画像を保存
            $filename = uniqid().'_'.$request->file('profile_image')->getClientOriginalName();
            $request->file('profile_image')->storeAs('public/profile', $filename);
            $user->profile_image = $filename;
        }

        $user->save();
        
        // 住所はaddressesテーブル
        $isFirstAddress = false;
        $address = $user->address;
        if (!$address) {
            $address = new \App\Models\Address();
            $address->user_id = $user->id;
            $isFirstAddress = true;
        }
        $address->postal_code = $request->input('zipcode');
        $address->address = $request->input('address');
        $address->building = $request->input('building');
        $address->save();

        // 初回登録時のみ商品一覧へ、それ以外はマイページ
        if ($isFirstAddress) {
            return redirect()->route('items.index')->with('success', 'プロフィールを登録しました');
        } else {
            return redirect()->route('profile.index')->with('success', 'プロフィールを更新しました');
        }
    }
}
