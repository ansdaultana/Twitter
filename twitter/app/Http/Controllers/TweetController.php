<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Http\Requests\StoreTweetRequest;
use App\Http\Requests\UpdateTweetRequest;
use App\Models\User;
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
        $search = Request::input('search');
        $followingIds = auth()->user()->following()->pluck('user_id');
           $Tweets= Tweet::with('user')->whereIn('user_id', $followingIds)
                ->withCount('likes')
                ->withCount('comments')
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
        }
        else
        {

            abort(403);
        }
    }

    public function store(StoreTweetRequest $request)
    {
        //
    }
    public function show(Tweet $tweet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet)
    {
        //
    }

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