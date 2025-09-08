<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('purchases')->truncate();

        // 外部キー制約を有効に戻す
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $purchases = [
            [
                'user_id' => 3, // 購入者のID
                'item_id' => 1, // 商品（腕時計）のID
                'status' => 'in_progress', // 取引中
                'payment_method' => 'credit_card',
                'price' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'item_id' => 2, 
                'status' => 'in_progress',
                'payment_method' => 'credit_card',
                'price' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'item_id' => 3, 
                'status' => 'in_progress',
                'payment_method' => 'credit_card',
                'price' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'item_id' => 6, 
                'status' => 'in_progress',
                'payment_method' => 'credit_card',
                'price' => 8000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'item_id' => 7, 
                'status' => 'in_progress',
                'payment_method' => 'credit_card',
                'price' => 3500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'item_id' => 8, 
                'status' => 'in_progress',
                'payment_method' => 'credit_card',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('purchases')->insert($purchases);

        // purchasesテーブルに挿入した商品のステータスを更新
        $itemIds = collect($purchases)->pluck('item_id')->unique();
        
        // 0:出品中, 1:取引中, 2:取引完了
        Item::whereIn('id', $itemIds)->update(['status' => 1]);
    }
}
