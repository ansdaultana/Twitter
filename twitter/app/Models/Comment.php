<?php

namespace App\Models;

use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function likes()
    {
        return $this->morphMany(Like::class, 'tweet');
    }
    public function user()

    {
        return $this->belongsTo(User::class);
    }
    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }
}
