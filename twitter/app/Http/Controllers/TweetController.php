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
    public function new()
    {
        return Inertia::render('Login');
    }

    public function index()
    {
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $followingIds = $loggedInUser->following()->pluck('user_id');
        //select tweets with user and likes so no n+1
        $Tweets = Tweet::with(['user', 'likes'])
            //there is scope function in tweet model which returns the tweets of followingid
            ->fromFollowedUsers($followingIds)
            ->withCount(['likes', 'comments'])
            ->addSelect([
                //counts the number of id values in the likes table and returns 1 if the count is greater than 0,
                // indicating that there are likes present. Otherwise, it returns 0, indicating no likes.
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('likeable_id', 'tweets.id')
                    ->where('likeable_type', Tweet::class)
                    ->where('user_id', $loggedInUser->id),
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
        $loggedInUser = auth()->user();
        $search = Request::input('search');
       
        $Comments = Comment::with(['user', 'likes'])
            ->withCount(['likes'])
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('likeable_id', 'comments.id')
                    ->where('likeable_type', Comment::class)
                    ->where('user_id', $loggedInUser->id),
            ])
            ->latest()
            ->get();
        return Inertia::render(
            'ShowTweet',
            [
                'tweet' => $tweet,
                'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
                'comments'=>$Comments,
                'authUser'=>$loggedInUser
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