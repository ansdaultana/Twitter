<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
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
        ->latest()
        ->get();

        return Inertia::render('UserShow', [
            "BeingVieweduser"=>$user,
            "tweets"=>$tweets,
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
                'profile'=>false,

            'auth_is_following_opened_user'=>$this->isFollowing($user->username),
         ]);
    }

    public function index(User $user)
    {
        $search = Request::input('search');
        $tweets = Tweet::with('user')
        ->where('user_id', $user->id)
        ->latest()
        ->get();
        $followerCount = DB::table('followers')->where('user_id',$user->id)->count();
        $followingCount = DB::table('followers')->where('follower_id',$user->id)->count();

        return Inertia::render('UserShow', [
            "BeingVieweduser"=>$user,
            "tweets"=>$tweets,
            'users' => $search ? User::query()
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('username', 'like', '%' . $search . '%');
                })
                ->where('id', '!=', auth()->user()->id)
                ->limit(20)
                ->get() : [],
                'profile'=>true,
                'followerCount' => $followerCount,
                'followingCount' => $followingCount,
            'auth_is_following_opened_user'=>$this->isFollowing($user->username),
         ]);
    }

     public function follow(User $user)
     {
         $loggedInUser = auth()->user();
         $targetUser = User::where('username',$user->username)->first();
         if (!$targetUser) {
             abort(404, 'User not found');
         }
         // Check if the logged-in user is already following the target user
         //go to auth user
         //in its following
         //check if the user_id is target_id
         //flower id will automaticaly be auth id  
         $isFollowing = $loggedInUser->following()->where('user_id', $targetUser->id)->exists();
         if ($isFollowing) {
             // Unfollow the user
             $loggedInUser->following()->detach($targetUser->id);
             $isFollowing = false;
         } else {
             // Follow the user
             $loggedInUser->following()->attach($targetUser->id);
             $isFollowing = true;
         }
         // Update the isFollowing status and return the response
         $response = [
             'isFollowing' => $isFollowing,
         ];
         return response()->json($response);
     }

    
}
