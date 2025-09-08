<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;

// 認証不要のルート
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 例：ログインページ
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 例：会員登録ページ
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/search', [ItemController::class, 'search'])->name('items.search');

// メール認証通知メールの再送信
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証リンクからのアクセス
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    $user = $request->user();
    if (!$user->address) {
        // 住所が未登録なら住所登録ページへリダイレクト
        return redirect()->route('address.edit', ['item_id' => 0]);
    }

    // 住所が登録済みなら商品一覧ページへリダイレクト
    return redirect()->route('items.index');
})->middleware(['auth', 'signed'])->name('verification.verify');
// 再送信処理
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証用メールを再送信しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 認証が必要なルートをまとめる
Route::middleware(['auth', 'verified'])->group(function () {
    // 商品出品（登録・保存）
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
    // サンクスページ
    Route::get('/purchase/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');
    // 住所編集
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');
    // 商品購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'form'])->name('purchase.form');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
    // コメント投稿
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comments.store');
    // いいね追加
    Route::post('/item/{item_id}/like', [ItemController::class, 'like'])->name('items.like');
    // いいね解除
    Route::delete('/item/{item_id}/like', [ItemController::class, 'unlike'])->name('items.unlike');
    // マイページ
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    // チャット関連ルート
    Route::get('/chat/{purchase}', [ChatController::class, 'show'])->name('chat');
    Route::post('/chat/{purchase}', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::put('/chat/{message}', [ChatController::class, 'update'])->name('chat.update');
    // 評価関連ルート
    Route::post('/rate', [RatingController::class, 'store'])->name('ratings.store');
    // 取引完了用ルート（ChatControllerに追加）
    Route::post('/chat/{purchase}/complete-transaction', [ChatController::class, 'completeTransaction'])->name('chat.complete');
});