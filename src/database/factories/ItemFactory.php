<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    protected $model = \App\Models\Item::class;

    public function definition()
    {
        return [
            'user_id'    => User::factory(),
            'name'       => $this->faker->word . '商品',
            'brand'      => $this->faker->company,
            'description'=> $this->faker->realText(50),
            'category'   => 'ファッション',
            'condition'  => '良好',
            'price'      => $this->faker->numberBetween(100, 50000),
            'img_url'    => 'items/dummy.jpg',
            'status'     => 0,
        ];
    }
}
