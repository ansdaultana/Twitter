<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Hashtag;
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

        $allTweets = Tweet::with(['user', 'likes'])
            ->where(function ($query) use ($followingIds) {
                $query->whereIn('user_id', $followingIds)
                    ->orWhereIn('id', function ($query) use ($followingIds) {
                        $query->select('tweet_id')
                            ->from('retweets')
                            ->whereIn('user_id', $followingIds);
                    });
            })
            ->whereNull('parent_tweet_id')
            ->withCount(['likes', 'replies', 'retweets'])
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
                'isReTweeted' => Retweet::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $loggedInUser->id)
                    ->limit(1),
                'retweetedBy' => Retweet::select('name')    
                    ->join('users', 'retweets.user_id', '=', 'users.id')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->limit(1),
            ])
            ->latest()
            ->get();
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
        $mutualFollowing = User::whereHas('followers', function ($query) use ($loggedInUser) {
            $query->whereIn('follower_id', $loggedInUser->following()->pluck('user_id'));
        })
            ->whereNotIn('id', $loggedInUser->following()->pluck('user_id'))->where('id', '!=', $loggedInUser->id)->take(2)->get();
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
            'tweets' => $allTweets,
            "admin" => "ansdaultana",
            "mutualFollowing" => $mutualFollowing,
            'hashtags'=>$Hashtag,
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
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
     
        
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
        'hashtags'=>$Hashtag,

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
            $tweet = Tweet::create($attributes);
            if (request('hashtags')) {
                $Hashtagss = request('hashtags');
                foreach ($Hashtagss as $tag) {
                    $existingTag = Hashtag::where('tag', $tag)->first();
                    if (!$existingTag) {
                        $newHash = Hashtag::create([
                            'tag' => $tag
                        ]);
                        $newHash->tweets()->attach($tweet->id);
                    } else {
                        $existingTag->tweets()->attach($tweet->id);
                    }
                }
            }
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
            if (request('hashtags')) {
                $Hashtagss = request('hashtags');
                foreach ($Hashtagss as $tag) {
                    $existingTag = Hashtag::where('tag', $tag)->first();
                    if (!$existingTag) {
                        $newHash = Hashtag::create([
                            'tag' => $tag
                        ]);
                        $newHash->tweets()->attach($tweet->id);
                    } else {
                        $existingTag->tweets()->attach($tweet->id);
                    }
                }
            }
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
            $tweet = Tweet::create($attributes);
            if (request('hashtags')) {
                $Hashtagss = request('hashtags');
                foreach ($Hashtagss as $tag) {
                    $existingTag = Hashtag::where('tag', $tag)->first();
                    if (!$existingTag) {
                        $newHash = Hashtag::create([
                            'tag' => $tag
                        ]);
                        $newHash->tweets()->attach($tweet->id);
                    } else {
                        $existingTag->tweets()->attach($tweet->id);
                    }
                }
            }
        } else {
            abort(403);
        }
    }
    public function show(User $user, Tweet $tweet)
    {
        $loggedInUser = auth()->user();
        $replies = Tweet::with('user', 'likes')
            ->withCount('likes', 'replies', 'retweets')
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
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
   
        
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
            'hashtags'=>$Hashtag,

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
        $isReTweeted = $tweet->retweets()->where([
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

    public function Hashtags($tag)
    {
        $hashtag = Hashtag::find($tag);
        if($hashtag)
        {
        $tagId=$hashtag->id;
        $loggedInUser = auth()->user();
        $search = Request::input('search');
        $followingIds = $loggedInUser->following()->pluck('user_id');
        $allTweets = Tweet::with(['user', 'likes'])
        ->whereNull('parent_tweet_id')
        ->whereHas('hashtags', function ($query) use ($tagId) {
            $query->where('hashtags.id', $tagId);
        })
        ->withCount(['likes', 'replies', 'retweets'])
        ->addSelect([
            'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                ->whereColumn('tweet_id', 'tweets.id')
                ->where('user_id', $loggedInUser->id)
                ->limit(1),
            'isReTweeted' => Retweet::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                ->whereColumn('tweet_id', 'tweets.id')
                ->where('user_id', $loggedInUser->id)
                ->limit(1),
            'retweetedBy' => Retweet::select('name')
                ->join('users', 'retweets.user_id', '=', 'users.id')
                ->whereColumn('tweet_id', 'tweets.id')
                ->limit(1),
        ])
        ->latest()
        ->get();
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
        $mutualFollowing = User::whereHas('followers', function ($query) use ($loggedInUser) {
            $query->whereIn('follower_id', $loggedInUser->following()->pluck('user_id'));
        })
            ->whereNotIn('id', $loggedInUser->following()->pluck('user_id'))->where('id', '!=', $loggedInUser->id)->take(2)->get();
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
            'tweets' => $allTweets,
            "admin" => "ansdaultana",
            "mutualFollowing" => $mutualFollowing,
            'hashtags'=>$Hashtag,
        ]);
        }
        else {
            
            abort(404);
        }
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