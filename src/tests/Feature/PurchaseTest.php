<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // CSRFミドルウェアを除外（テスト用）
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function ユーザーが商品を購入できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 0]);

        $response = $this->actingAs($user)->post(route('purchase.store', $item->id), [
            'payment_method' => 'クレジットカード',
            'shipping' => '東京都新宿区テスト1-2-3',
        ]);

        $response->assertRedirect(); // StripeのURL等にリダイレクト
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'クレジットカード',
            'shipping' => '東京都新宿区テスト1-2-3',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 1,
        ]);
    }

    /** @test */
    public function 購入した商品が_sold_と表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);

        $response = $this->actingAs($user)->get(route('items.show', $item->id));

        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    /** @test */
    public function 購入した商品がマイページに表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 1]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'クレジットカード',
            'shipping' => '東京都世田谷区1-1-1',
            'price' => $item->price,
        ]);

        // マイページの購入タブを開く
        $response = $this->actingAs($user)->get(route('profile.index', ['tab' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    public function test_支払い方法が反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 0]);

        $this->actingAs($user)->post(route('purchase.store', $item->id), [
            'payment_method' => 'コンビニ払い',
            'shipping' => '東京都杉並区1-2-3',
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ払い',
        ]);
    }

    public function test_配送先が反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 0]);

        $this->actingAs($user)->post(route('purchase.store', $item->id), [
            'payment_method' => 'クレジットカード',
            'shipping' => '大阪市中央区1-1-1',
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping' => '大阪市中央区1-1-1',
        ]);
    }
}
