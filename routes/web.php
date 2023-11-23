<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// admin routes
Route::get('/admins_only', function () {
  if (!Gate::allows('admin-access')) {
    return 'You are not an admin.';
  }
    return 'for admins only';
});

// User Routes
Route::get('/', [UserController::class, 'showCorrectHomePage'])->name('login');
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('must-be-logged-in');
Route::get('/manage-avatar', [UserController::class, 'showAvatarForm'])->middleware('must-be-logged-in');
Route::post('/manage-avatar', [UserController::class, 'uploadAvatar'])->middleware('must-be-logged-in');

// Blog Routes
Route::get('/create-post', [PostController::class, 'showCreateForm'])->middleware('must-be-logged-in');
Route::post('/create-post', [PostController::class, 'storeNewPost'])->middleware('must-be-logged-in');
Route::get('/post/{post}', [PostController::class, 'viewSinglePost']);
Route::get('/post/{post}/edit', [PostController::class, 'showEditForm'])->middleware('must-be-logged-in');
Route::put('/post/{post}', [PostController::class, 'updatePost'])->middleware('must-be-logged-in');
Route::delete('/post/{post}', [PostController::class, 'deletePost']);
Route::get('/posts/search/{search}', [PostController::class, 'searchPosts']);

// Profile Routes
Route::get('/profile/{user}', [UserController::class, 'viewProfile']);
