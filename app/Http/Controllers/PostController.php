<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\AuthorizationException;


class PostController extends Controller
{
    //
    public function storeNewPost(Request $request) {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'body' => 'required|min:3|max:10000',
        ]);
    
        // Ensure the user is authenticated
        if (!Auth::check()) {
            // Handle the case where no user is logged in
            // For example, redirect to the login page or show an error message
            return redirect('login')->withErrors('You must be logged in to create a post.');
        }
    
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => Auth::id(), // Retrieve the ID of the currently authenticated user
        ]);
    
        return redirect("/post/{$post->id}")->with('success', 'Post was created successfully.');
    }

    public function showCreateForm() {
        return view('create-post');
    }

    public function viewSinglePost(Post $post) {
        return view('single-post', [
            'post' => $post,
        ]);
    }

    public function showEditForm(Post $post) {
        try {
            $this->authorize('update', $post);
        } catch (AuthorizationException $e) {
            return $e->getMessage() . ". You do not have permission to edit this post.";
        }
    
        return view('edit-post', [
            'post' => $post,
        ]);
    }

    public function updatePost(Post $post, Request $request) {
        try {
            $this->authorize('update', $post);
        } catch (AuthorizationException $e) {
            return $e->getMessage() . '. You do not have permission to update this post.';
        }

        $request->validate([
            'title' => 'required|min:3|max:255',
            'body' => 'required|min:3|max:10000',
        ]);

        $post->title = $request->title;
        $post->body = $request->body;
        $post->save();

        return redirect("/post/{$post->id}")->with('success', 'Post was updated successfully.');
    }

    public function deletePost(Post $post) {
    
        try {
            $this->authorize('delete', $post);
        } catch (AuthorizationException $e) {
            // Redirect back with an error message if the user is not authorized
            return $e->getMessage() .". You do not have permission to delete this post.";
        }
        
        $post->delete();
    
        return redirect('/')->with('success', 'Post was deleted successfully.');
    }

    public function searchPosts($search) {
    
        $posts = Post::where('title', 'like', "%{$search}%")
            ->orWhere('body', 'like', "%{$search}%")
            ->get();
    
        return $posts;
    }
    
}
