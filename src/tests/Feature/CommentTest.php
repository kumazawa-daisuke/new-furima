<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // CSRFトークン検証はスキップ（フォーム送信の簡略化）
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_ログインユーザーがコメントできる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('comments.store', $item->id), [
            'content' => 'これはテストコメントです。',
        ]);

        $response->assertRedirect(route('items.show', $item->id));

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);
    }

    public function test_非ログインユーザーはコメントできない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item->id), [
            'content' => 'ログインしていないコメント',
        ]);

        // Laravelの既定ではゲストは login へリダイレクトされる
        $response->assertRedirect(route('login'));
    }

    public function test_コメントが空欄だとバリデーションエラー()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->from(route('items.show', $item->id)) // 戻り先を設定
                         ->post(route('comments.store', $item->id), [
                             'content' => '',
                         ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['content']);
    }

    public function test_コメントが255文字を超えるとバリデーションエラー()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->from(route('items.show', $item->id))
                         ->post(route('comments.store', $item->id), [
                             'content' => str_repeat('あ', 256),
                         ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors(['content']);
    }
}
