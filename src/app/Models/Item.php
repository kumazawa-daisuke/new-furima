<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'category',
        'condition',
        'price',
        'img_url',
        'status',
    ];

    // 出品者（User）リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // 購入履歴（Purchase）リレーション
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    
    // コメントリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
