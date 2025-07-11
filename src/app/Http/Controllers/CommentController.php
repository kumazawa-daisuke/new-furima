<?php

namespace App\Http\Controllers;


use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // コメント投稿
    public function store(CommentRequest $request, $item_id)
    {
        $validated = $request->validated();

        // コメント保存
        $comment = new Comment();
        $comment->user_id = Auth::id();      // ログインユーザーID
        $comment->item_id = $item_id;        // 紐付く商品ID
        $comment->content = $validated['content'];
        $comment->save();

        return redirect()->route('items.show', $item_id)->with('success', 'コメントを投稿しました');
    }
}
