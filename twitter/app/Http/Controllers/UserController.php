<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Hashtag;
use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
    public function get()
    {
        return Inertia::render('Login');
    }
    public function new()
    {
        return Inertia::render('Register');
    }
    //
    public function isFollowing($username)
    {
        $loggedInUser = auth()->user();
        $AmIFollowingThisUser = User::where('username', $username)->first();

        if ($AmIFollowingThisUser && $loggedInUser->following()->where('user_id', $AmIFollowingThisUser->id)->exists()) {
            return true;
        }
        return false;
    }

    public function show(User $user)
    {
        $loggedInUser = auth()->user();

        $search = Request::input('search');
        $tweets = Tweet::with('user')
            ->where('user_id', $user->id)
            ->withCount('likes','retweets','replies')
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $user->id)
                    ->limit(1)
            ])
            ->whereNull('parent_tweet_id')
            ->latest()
            ->get();
            $Notificationscount = $loggedInUser->unreadNotifications->count();
          
        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();
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
      $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
        return Inertia::render('UserShow', [
            "BeingVieweduser" => $user,
        'hashtags'=>$Hashtag,
        'Notificationscount'=>$Notificationscount,
            "mutualFollowing" => $mutualFollowing,

            "admin" => "ansdaultana",
            "tweets" => $tweets,
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
            'profile' => false,
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'auth_is_following_opened_user' => $this->isFollowing($user->username),
        ]);
    }

    public function index(User $user)
    {
        $loggedInUser = auth()->user();

        $search = Request::input('search');
        $tweets = Tweet::with('user')
            ->where('user_id', $user->id)
            ->withCount('likes','retweets','replies')

            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $user->id)
                    ->limit(1)
            ])
            ->whereNull('parent_tweet_id')
            
            ->latest()
            ->get();
        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();
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
        $Notificationscount = $loggedInUser->unreadNotifications->count();
     
        
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
        return Inertia::render('UserShow', [
            "BeingVieweduser" => $user,
        'hashtags'=>$Hashtag,
        'Notificationscount'=>$Notificationscount,

            "mutualFollowing" => $mutualFollowing,
            "tweets" => $tweets,
            "admin" => "ansdaultana",
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
            'profile' => true,
            'followerCount' => $followerCount,
            'followingCount' => $followingCount,
            'auth_is_following_opened_user' => $this->isFollowing($user->username),
        ]);
    }

    public function follow(User $user)
    {
        $loggedInUser = auth()->user();
        $targetUser = User::where('username', $user->username)->first();
        if (!$targetUser) {
            abort(404, 'User not found');
        }
        if ($targetUser->username === $loggedInUser->username) {
            abort(403, 'Forbidden');

        }

        $isFollowing = $loggedInUser->following()->where('user_id', $targetUser->id)->exists();
        if ($isFollowing) {
            $loggedInUser->following()->detach($targetUser->id);
            $isFollowing = false;
        } else {
            $loggedInUser->following()->attach($targetUser->id);
            $isFollowing = true;
            $notificationData=[
                'user_id'=>$targetUser->id,
                'follower_id'=>$loggedInUser->id,
                'follower_name'=>$loggedInUser->name,
                'follower_profile'=>$loggedInUser->profile,
            ];
            $targetUser->notify(new NewFollower($notificationData));
        }
        $response = [
            'isFollowing' => $isFollowing,
        ];
        return response()->json($response);
    }

    public function showfollower(User $user)
    {
        $followers = $user->followers()->latest()->get()->map(function ($user) {
            $user['isFollowing'] = $this->isFollowing($user->username);
            $user['isprofile'] = auth()->user()->username === $user->username;
            $user['isHovered'] = false;
            return $user;
        });

        $loggedInUser = auth()->user();
        $Notificationscount = $loggedInUser->unreadNotifications->count();

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

        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();
        return Inertia::render(
            'Followers',
            [
        'Notificationscount'=>$Notificationscount,

                "admin" => "ansdaultana",
                "mutualFollowing" => $mutualFollowing,
                'hashtags'=>$Hashtag,

                'followers' => $followers,
                "BeingVieweduser" => $user,
                'users' => $search ? User::query()
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->where('id', '!=', auth()->user()->id)
                    ->limit(20)
                    ->get() : [],

            ]
        );
    }
    public function showfollowing(User $user)
    {
        $loggedInUser = auth()->user();

        $search = Request::input('search');
        $following = $user->following()->latest()->get()->map(function ($user) {
            $user['isFollowing'] = $this->isFollowing($user->username);
            $user['isprofile'] = auth()->user()->username === $user->username;
            $user['isHovered'] = false;

            return $user;
        });
        $Notificationscount = $loggedInUser->unreadNotifications->count();
       
        
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
        $Hashtag = Hashtag::withCount('tweets')->orderBy('tweets_count', 'desc')->take(4)->get();

        return Inertia::render(

            'Following',
            [
                "mutualFollowing" => $mutualFollowing,
                'hashtags'=>$Hashtag,
                'Notificationscount'=>$Notificationscount,

                "admin" => "ansdaultana",
                'following' => $following,
                "BeingVieweduser" => $user,
                'users' => $search ? User::query()
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('username', 'like', '%' . $search . '%');
                    })
                    ->where('id', '!=', auth()->user()->id)
                    ->limit(20)
                    ->get()
                : [],

            ]
        );
    }


    public function updateprofile(User $user)
    {
        $attributes = request()->validate([
            'name' => 'string|min:3',
        ]);
        if (auth()->check() && $user->id === auth()->id()) {
            if (request()->hasFile('profile')) {
                $uploadedProfile = request('profile')->storeOnCloudinary();
                $user->profile = $uploadedProfile->getSecurePath();
            }
            if (request()->hasFile('cover')) {
                $uploadedCover = request('cover')->storeOnCloudinary();
                $user->cover = $uploadedCover->getSecurePath();
            }
            $user->name = request('name');
            $user->save();
        }
    }

}