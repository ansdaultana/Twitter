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
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function scopeFromFollowedUsers($query, $followingIds)
{
    return $query->whereIn('user_id', $followingIds);
}
}
