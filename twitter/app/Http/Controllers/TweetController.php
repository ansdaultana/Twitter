<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Retweet;
use App\Models\Tweet;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Request;

class TweetController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $followingIds = $loggedInUser->following()->pluck('user_id');
        $Tweets = Tweet::with(['user', 'likes'])
            ->whereIn('user_id', $followingIds)
            ->withCount(['likes', 'replies','retweets'])
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
                    'isReTweeted' => Retweet::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
            ])
            ->whereNull('parent_tweet_id')
            ->latest()
            ->get();
        $mutualFollowing = User::whereHas('followers', function ($query) use ($loggedInUser) {
            $query->whereIn('follower_id', $loggedInUser->following()->pluck('user_id'));
        })
            ->whereNotIn('id', $loggedInUser->following()->pluck('user_id'))
            ->where('id', '!=', $loggedInUser->id)
            ->take(2)
            ->get();
        $mutualFollowing = $mutualFollowing->map(function ($user) {
            $user->is_following = false;
            return $user;
        });


        return Inertia::render('Feed', [
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->id())
                ->limit(20)
                ->get() : [],
            'tweets' => $Tweets,
            "admin" => "ansdaultana",
            "mutualFollowing" => $mutualFollowing,

        ]);
    }

    public function explore()
    {
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $followingIds = $loggedInUser->following()->pluck('user_id');
        $Tweets = Tweet::with(['user', 'likes'])
            ->whereIn('user_id', $followingIds)
            ->withCount(['likes', 'replies'])
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
            ])
            ->whereNull('parent_tweet_id')
            ->latest()
            ->get();
        $loggedInUser = auth()->user();


        $mutualFollowing = User::whereHas('followers', function ($query) use ($loggedInUser) {
            $query->whereIn('follower_id', $loggedInUser->following()->pluck('user_id'));
        })
            ->whereNotIn('id', $loggedInUser->following()->pluck('user_id'))
            ->where('id', '!=', $loggedInUser->id)
            ->take(10)
            ->get();

        $mutualFollowing = $mutualFollowing->map(function ($user) {
            $user->is_following = false;
            return $user;
        });

        return Inertia::render('ExplorePage', [
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->id())
                ->limit(20)
                ->get() : [],
            'tweets' => $Tweets,
            "admin" => "ansdaultana",
            "mutualFollowing" => $mutualFollowing,
        ]);
    }
    public function create()
    {

        $attributes = request()->validate([
            'text' => 'required|min:3',
        ]);
        if (auth()->check()) {
            $attributes['user_id'] = Auth::id();
            if (request()->file('image')) {
                $uploadedImage = request()->file('image')->storeOnCloudinary();
                $attributes['image'] = $uploadedImage->getSecurePath();
            } else if (request()->file('video')) {

                $uploadedVideo = request()->file('video')->storeOnCloudinary();
                $attributes['video'] = $uploadedVideo->getSecurePath();

            }
            Tweet::create($attributes);
        } else {

            abort(403);
        }
    }

    public function edit(Tweet $tweet)
    {
        $attributes = request()->validate([
            'text' => 'required|min:3',

        ]);
        if (auth()->check() && $tweet->user->id === auth()->id()) {
            if (request('image')) {
                $uploadedImage = request('image')->storeOnCloudinary();
                $attributes['image'] = $uploadedImage->getSecurePath();
                $attributes['video'] = null;

            } else if (request('video')) {
                $uploadedVideo = request('video')->storeOnCloudinary();
                $attributes['video'] = $uploadedVideo->getSecurePath();
                $attributes['image'] = null;

            }
            $tweet->update($attributes);
        } else {
            abort(403);
        }
    }

    public function delete(Tweet $tweet)
    {

        if (auth()->check() && auth()->id() === $tweet->user->id) {

            $tweet->delete();

        } else {

            abort(403);
        }

    }
    public function addReply(Tweet $tweet)
    {
        //
        $attributes = request()->validate([
            'text' => 'required|min:3',
        ]);
        if (auth()->check()) {
            $attributes['user_id'] = auth()->id();
            $attributes['parent_tweet_id'] = $tweet->id;
            if (request()->file('image')) {
                $uploadedImage = request()->file('image')->storeOnCloudinary();
                $attributes['image'] = $uploadedImage->getSecurePath();

            } else if (request()->file('video')) {
                $uploadedVideo = request()->file('video')->storeOnCloudinary();
                $attributes['video'] = $uploadedVideo->getSecurePath();

            }
            Tweet::create($attributes);
        } else {
            abort(403);
        }
    }
    public function show(User $user, Tweet $tweet)
    {
        $loggedInUser = auth()->user();
        $replies = Tweet::with('user', 'likes')
            ->withCount('likes','replies','retweets')
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
                    'isReTweeted' => Retweet::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
            ])->where('parent_tweet_id', $tweet->id)
            ->latest()
            ->get();
        $search = Request::input('search');
        $mutualFollowing = User::whereHas('followers', function ($query) use ($loggedInUser) {
            $query->whereIn('follower_id', $loggedInUser->following()->pluck('user_id'));
        })
            ->whereNotIn('id', $loggedInUser->following()->pluck('user_id'))
            ->where('id', '!=', $loggedInUser->id)
            ->take(2)
            ->get();

        $mutualFollowing = $mutualFollowing->map(function ($user) {
            $user->is_following = false;
            return $user;
        });
        return Inertia::render(
            'ShowTweet',
            [
                "mutualFollowing" => $mutualFollowing,
                'tweet' => $tweet,
                'user' => $user,
                'likes_count' => $tweet->likes()->count(),
                'replies_count' => $tweet->replies()->count(),
                'retweets_count' => $tweet->retweets()->count(),
                'users' => $search
                ? User::query()
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->where('id', '!=', auth()->id())
                    ->limit(20)
                    ->get()
                : [],
                'replies' => $replies,
                'authUser' => $loggedInUser,
                'isLiked' => $loggedInUser->likes->where('tweet_id', $tweet->id)->count() > 0,
                'isReTweeted' => $loggedInUser->retweets->where('tweet_id', $tweet->id)->count() > 0,
                "admin" => "ansdaultana",

            ]
        );


    }

    public function retweet(Tweet $tweet)
    {
        $loggedInUser = auth()->user();
    
        try {
            $checkTweet = Tweet::findOrFail($tweet->id);
        } catch (ModelNotFoundException $e) {
            abort(404, 'Tweet not found');
        }
        $isReTweeted= $tweet->retweets()->where([
            'tweet_id' => $tweet->id,
            'user_id' => $loggedInUser->id,
        ])->exists();
    
        if ($isReTweeted) {
            $tweet->retweets()->where([
                'tweet_id' => $tweet->id,
                'user_id' => $loggedInUser->id,
            ])->delete();
        } else {
            $retweet = new Retweet([
                'user_id' => $loggedInUser->id,
                'tweet_id' => $tweet->id,
            ]);
            $retweet->save();
        }
        $isReTweeted = !$isReTweeted;
        $response = [
            'isReTweeted' => $isReTweeted,
        ];
    
        return response()->json($response);
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