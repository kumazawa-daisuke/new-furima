<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class); // テスト中はCSRF無効化
    }

    public function test_出品商品を正しく編集できる()
    {
        // テストユーザーと出品商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '古い商品名',
            'price' => 5000,
            'description' => '古い説明文',
        ]);

        // ログインして編集リクエスト送信（URL直書き）
        $response = $this->actingAs($user)->post("/items/{$item->id}/update", [
            'name' => '新しい商品名',
            'price' => 9999,
            'description' => '新しい説明文',
            'category' => ['家電', '本'],
            'condition' => '目立った傷や汚れなし',
        ]);

        // リダイレクト確認
        $response->assertRedirect();

        // データベースが更新されているか確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => '新しい商品名',
            'price' => 9999,
            'description' => '新しい説明文',
        ]);
    }
}
