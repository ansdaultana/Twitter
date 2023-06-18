<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'text',
    ];
    public function user()
    {

        return $this->belongsTo(User::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    
    public function parentTweet()
    {
        return $this->belongsTo(Tweet::class, 'parent_tweet_id');
    }

    public function replies()
    {
        return $this->hasMany(Tweet::class, 'parent_tweet_id');
    }
        public function scopeFromFollowedUsers($query, $followingIds)
{
    return $query->whereIn('user_id', $followingIds);
}
}
