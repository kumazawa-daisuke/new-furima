<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::insert([
            [
                'user_id' => 1,
                'name' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'category' => 'ファッション',
                'condition' => '良好',
                'price' => 15000,
                'img_url' => 'items/watch.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'category' => '家電',
                'condition' => '目立った傷や汚れなし',
                'price' => 5000,
                'img_url' => 'items/hdd.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'category' => '食品',
                'condition' => 'やや傷や汚れあり',
                'price' => 300,
                'img_url' => 'items/onion.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'brand' => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'category' => 'ファッション',
                'condition' => '状態が悪い',
                'price' => 4000,
                'img_url' => 'items/shoes.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'brand' => '西芝',
                'description' => '高性能なノートパソコン',
                'category' => '家電',
                'condition' => '良好',
                'price' => 45000,
                'img_url' => 'items/pc.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 2,
                'name' => 'マイク',
                'brand' => 'なし',
                'description' => '高品質のレコーディング用マイク',
                'category' => '家電',
                'condition' => '目立った傷や汚れなし',
                'price' => 8000,
                'img_url' => 'items/mic.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 2,
                'name' => 'ショルダーバッグ',
                'brand' => 'なし',
                'description' => 'おしゃれなレザーショルダーバッグ',
                'category' => 'ファッション',
                'condition' => 'やや傷や汚れあり',
                'price' => 3500,
                'img_url' => 'items/shoulderbag.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'category' => '生活雑貨',
                'condition' => '状態が悪い',
                'price' => 500,
                'img_url' => 'items/tumbler.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 2,
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'category' => '生活雑貨',
                'condition' => '良好',
                'price' => 4000,
                'img_url' => 'items/coffeemill.jpg',
                'status' => 0,
            ],
            [
                'user_id' => 2,
                'name' => 'メイクセット',
                'brand' => 'なし',
                'description' => '便利なメイクアップセット',
                'category' => 'コスメ',
                'condition' => '目立った傷や汚れなし',
                'price' => 2500,
                'img_url' => 'items/makeup.jpg',
                'status' => 0,
            ],
        ]);
    }
}
