<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Tweet;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Request;
use SebastianBergmann\Environment\Console;

class TweetController extends Controller
{


    public function index()
    {
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $followingIds = $loggedInUser->following()->pluck('user_id');
        $Tweets = Tweet::with(['user', 'likes'])
            ->fromFollowedUsers($followingIds)
            ->withCount(['likes', 'replies'])
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
            ])
            ->latest()
            ->get();
        return Inertia::render('Feed', [
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
            'tweets' => $Tweets,

        ]);
    }

    public function create()
    {
        $attributes = request()->validate([
            'text' => 'required|min:3',
        ]);

        if (auth()->check()) {
            $attributes['user_id'] = Auth::id();
            Tweet::create($attributes);
        } else {

            abort(403);
        }
    }


    public function edit(Tweet $tweet)
    {
        //

        $attributes = request()->validate([
            'text' => 'required|min:3',
        ]);

        if (auth()->check() && $tweet->user->id === auth()->user()->id) {

            $tweet['text'] = $attributes['text'];
            $tweet->save();
        } else {

            abort(403);
        }
    }
    public function delete(Tweet $tweet)
    {

        if (auth()->check() && auth()->user()->id === $tweet->user->id) {

            $tweet->delete();

        } else {

            abort(403);
        }

    }
    public function store(StoreTweetRequest $request)
    {
        //
    }
    public function show(User $user, Tweet $tweet)
    {
       
        //  Comment::with(['user', 'likes'])
        //     ->withCount(['likes'])
        //     ->addSelect([
        //         'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
        //             ->where('likeable_type', Comment::class)
        //             ->where('user_id', $loggedInUser->id),
        //     ])
        //     ->latest()
        //     ->get();
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $replies = null;

        return Inertia::render(
            'ShowTweet',
            [
                'tweet' => $tweet,
                'user' => $user,
                'likes_count' => $tweet->likes()->count(),
                'users' => $search
                    ? User::query()
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('username', 'like', '%' . $search . '%');
                        })
                        ->where('id', '!=', auth()->user()->id)
                        ->limit(20)
                        ->get()
                    : [],
                'replies' => $replies,
                'authUser' => $loggedInUser,
                'isLiked' => $user->likes->where('tweet_id', $tweet->id)->count() > 0?true:false,
            ]
        );
        

    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTweetRequest $request, Tweet $tweet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
        //
    }
}