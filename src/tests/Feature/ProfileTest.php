<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); // CSRFチェック無効化
    }

    /**
     * 出品商品タブで、自分が出品した商品が表示されることを確認
     */
    public function test_マイページで出品商品が表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品テスト',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=sell');

        $response->assertStatus(200);
        $response->assertSee('出品商品テスト');
    }

    /**
     * 購入商品タブで、自分が購入した商品が表示されることを確認
     */
    public function test_マイページで購入商品が表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => '購入商品テスト',
            'status' => 1,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'クレジットカード',
            'shipping' => '東京都港区1-1-1',
            'price' => $item->price,
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=buy');

        $response->assertStatus(200);
        $response->assertSee('購入商品テスト');
    }

    /**
     * タブ未指定時は出品商品が表示されることを確認（デフォルト挙動）
     */
    public function test_マイページでタブ未指定時は出品商品が表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'デフォルト出品商品',
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSee('デフォルト出品商品');
    }

    public function test_購入履歴に商品が表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'クレジットカード',
            'shipping' => '渋谷区1-1-1',
            'price' => $item->price,
        ]);

        $response = $this->actingAs($user)->get(route('profile.index', ['tab' => 'buy']));
        $response->assertSee($item->name);
    }

    public function test_プロフィール画面に各情報が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'test.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertSee('テストユーザー');
        $response->assertSee('出品した商品');
        $response->assertSee('購入した商品');
    }
}
