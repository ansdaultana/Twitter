<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
use App\Models\Tweet;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [UserController::class, 'get'])->name('authcheck');
Route::get('/register', [UserController::class, 'new']);


Route::middleware('auth')->group(function () {
    Route::get('/home', [TweetController::class, 'index']);
    Route::get('/explore', [TweetController::class, 'explore']);

    Route::get('/users/{user:username}', [UserController::class, 'show'])->name('user.show');
    Route::get('/{user:username}', [UserController::class, 'index'])->name('user.index');

    Route::get('/{user:username}/follower', [UserController::class, 'showfollower'])->name('userfollowers');
    Route::get('/{user:username}/following', [UserController::class, 'showfollowing'])->name('userfollowing');
    Route::post('/createtweet', [TweetController::class, 'create'])->name('tweet.create');
    Route::post('/users/{user:username}/follow', [UserController::class, 'follow'])->name('user.follow');
    Route::post('/tweets/{tweet:id}/like', [LikeController::class, 'like']);
    Route::post('/edittweet/{tweet:id}', [TweetController::class, 'edit']);
    Route::post('/deletetweet/{tweet:id}', [TweetController::class, 'delete']);
    Route::get('/{user:username}/tweets/{tweet:id}',[TweetController::class,'show']);
    Route::post('/replytweet/{tweet:id}',[TweetController::class,'addReply']);
    Route::post('/retweet/{tweet:id}',[TweetController::class,'retweet']);
    Route::get('/tags/{tag}',[TweetController::class,'Hashtags']);
    Route::post('/updateprofile/{user:id}',[UserController::class,'updateprofile']);



});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';