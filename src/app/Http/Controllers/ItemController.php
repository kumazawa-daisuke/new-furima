<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest; 

class ItemController extends Controller
{
    // 商品一覧
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');
        $q = $request->input('q');

        if ($tab === 'mylist' && auth()->check()) {
            // マイリスト（いいねした商品）
            $itemsQuery = auth()->user()->likedItems();
            if ($q) {
                $itemsQuery->where('name', 'like', "%{$q}%");
            }
            $items = $itemsQuery->get();
        } else {
            $items = \App\Models\Item::when($q, function($query, $q) {
                return $query->where('name', 'like', "%{$q}%");
            })
            ->when(auth()->check(), function($query) {
                return $query->where('user_id', '<>', auth()->id());
            })
            ->get();
        }

        return view('items_index', [
            'items' => $items,
            'tab' => $tab,
            'search_word' => $q,
        ]);
    }

    public function show($item_id)
    {
        $item = Item::with('comments.user')->findOrFail($item_id);
        return view('items_show', compact('item'));
    }

    // 商品出品画面
    public function sell()
    {
        return view('items_sell');
    }

    // 例: 出品フォーム表示用
    public function create()
    {
        $categories = [
            'ファッション', '家電', 'インテリア', 'レディース', 'メンズ', 'コスメ',
            '本', 'ゲーム', 'スポーツ', 'キッチン', 'ハンドメイド', 'アクセサリー',
            'おもちゃ', 'ベビー・キッズ'
        ];

        $statuses = [
            '良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'
        ];

        return view('items_create', compact('categories', 'statuses'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像アップロード処理
        $img_path = null;
        if ($request->hasFile('image')) {
            $img_path = $request->file('image')->store('items', 'public');
        }

        $categories = $request->input('category', []);
        $category_str = implode(',', $categories);

        // モデルに保存
        $item = new Item();
        $item->category = $category_str;
        $item->condition = $validated['condition'];
        $item->name = $validated['name'];
        $item->brand = $validated['brand'] ?? null;
        $item->description = $validated['description'] ?? null;
        $item->price = $validated['price'];
        $item->img_url = $img_path; // 画像パスを保存
        $item->user_id = auth()->id(); // 出品者IDなど
        $item->save();

        return redirect()->route('items.index')->with('success', '出品しました！');
    }

    // いいね追加
    public function like($item_id)
    {
        $user_id = Auth::id();

        // すでにLikeしていないかチェック
        $exists = Like::where('user_id', $user_id)->where('item_id', $item_id)->exists();

        if (!$exists) {
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
        }

        // 戻り先は必要に応じて
        return back()->with('success', 'いいねしました！');
    }

    // いいね解除
    public function unlike($item_id)
    {
        $user_id = Auth::id();

        Like::where('user_id', $user_id)->where('item_id', $item_id)->delete();

        return back()->with('success', 'いいねを取り消しました！');
    }

    
    public function search(Request $request)
    {
        $query = $request->input('q');

        // 商品名で部分一致検索
        $items = \App\Models\Item::where('name', 'like', "%{$query}%")->get();

        // タブ状態やメッセージも渡せる
        return view('items_index', [
            'items' => $items,
            'tab' => 'search',
            'search_word' => $query,
        ]);
    }
}
