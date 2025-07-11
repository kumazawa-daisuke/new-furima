<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function address()
    {
        return $this->hasOne(Address::class, 'user_id');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/profile/' . $this->profile_image);
        } else {
            return asset('storage/images/default_user.png');
        }
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }

    public function likedItems()
    {
        // Likeテーブルを経由して商品を取得
        return $this->belongsToMany(\App\Models\Item::class, 'likes', 'user_id', 'item_id');
    }

    // 出品した商品
    public function sellingItems() {
        return $this->hasMany(Item::class, 'user_id');
    }
    // 購入した商品
    public function boughtItems() {
        return $this->belongsToMany(Item::class, 'purchases', 'user_id', 'item_id');
    }

    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class, 'user_id');
    }

}

