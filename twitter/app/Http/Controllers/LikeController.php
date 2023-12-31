<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\UpdateLikeRequest;
use App\Models\Tweet;
use App\Notifications\NewLike;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function like(Tweet $tweet)
    {
        $loggedInUser = auth()->user();
        try {
            $checkTweet = Tweet::findOrFail($tweet->id);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Tweet not found');
        }
    
        $isLiked = $tweet->likes()->where([
            'tweet_id' => $tweet->id,
            'user_id' => $loggedInUser->id,
        ])->exists();
    
        if ($isLiked) {
            $tweet->likes()->where([
                'tweet_id' => $tweet->id,
                'user_id' => $loggedInUser->id,
            ])->delete();
        } else {
            

            $tweetowner=$tweet->user;
            $notificationData=[
                'tweet_id'=>$tweet->id,
                'liker_id'=>$loggedInUser->id,
                'liker_name'=>$loggedInUser->name,
                'liker_profile'=>$loggedInUser->profile,
            ];
            $tweetowner->notify(new NewLike($notificationData));
            $like = new Like([
                'user_id' => $loggedInUser->id,
                'tweet_id' => $tweet->id,
            ]);
            $like->save();

            
        }
    
        $isLiked = !$isLiked;
    
        $response = [
            'isLiked' => $isLiked,
        ];
    
        return response()->json($response);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLikeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLikeRequest $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Like $like)
    {
        //
    }
}