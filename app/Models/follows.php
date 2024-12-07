<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class follows extends Model
{
    use HasFactory;

    // السماح بملء الأعمدة المحددة (Mass Assignment)
    protected $fillable = ['follower_id', 'following_id'];

    // علاقة مع المستخدم الذي يقوم بالمتابعة
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    // علاقة مع المستخدم الذي يتم متابعته
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }}
