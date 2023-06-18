<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
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
        $search = Request::input('search');
        $tweets = Tweet::with('user')
            ->where('user_id', $user->id)
            ->withCount('likes')
            ->withCount('replies')
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $user->id)
                    ->limit(1)
            ])
            ->latest()
            ->get();

        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();
        return Inertia::render('UserShow', [
            "BeingVieweduser" => $user,
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
        $search = Request::input('search');
        $tweets = Tweet::with('user')
            ->where('user_id', $user->id)
            ->withCount('likes')
            ->withCount('replies')
            ->addSelect([
                'isLiked' => Like::selectRaw('IF(COUNT(id) > 0, 1, 0)')
                    ->whereColumn('tweet_id', 'tweets.id')
                    ->where('user_id', $user->id)
                    ->limit(1)
            ])
            ->latest()
            ->get();
        $followerCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        return Inertia::render('UserShow', [
            "BeingVieweduser" => $user,
            "tweets" => $tweets,
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
            $user['profile'] = auth()->user()->username === $user->username;
            $user['isHovered'] = false;
            return $user;
        });
        
        $search = Request::input('search');

        return Inertia::render(
            'Followers',
            [
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
        $search = Request::input('search');
        $following = $user->following()->latest()->get()->map(function ($user) {
            $user['isFollowing'] = $this->isFollowing($user->username);
            $user['profile'] = auth()->user()->username === $user->username;
            $user['isHovered'] = false;

            return $user;
        });
        return Inertia::render(
            'Following',
            [
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
}