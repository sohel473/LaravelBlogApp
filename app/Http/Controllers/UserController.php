<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function showCorrectHomePage() {
        if (Auth::check()) {
            return view('homepage-feed');
        } else {
            return view('homepage');
        }
    }

    public function register(Request $request) {
        $request->validate([
            'username' => ['required', 'min:3', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'max:255', 'confirmed'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email, 
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        session()->flash('success', 'You have been registered successfully.');

        return redirect('/');
    }

    public function login(Request $request) {
        $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required',
        ]);

        $user = User::where('username', $request->loginusername)->first();

        if ($user && Hash::check($request->loginpassword, $user->password)) {
            // Log the user in
            Auth::login($user);

            $request->session()->regenerate();

            session()->flash('success', 'You have been logged in successfully.');

            return redirect('/');

        }

        session()->flash('failure', 'The provided credentials do not match our records.');

        return back()->withInput($request->only('loginusername'));
    }

    public function logout(Request $request) {
        Auth::logout();
    
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'You have been logged out successfully.');
    
        return redirect('/');
    }    

    public function viewProfile(User $user) {
        return view('profile', ['username' => $user->username, 'posts' => $user->posts()->latest()->get(), 'postCount' => $user->posts()->count()]);
    }

    public function showAvatarForm() {
        return view('avatar-form');
    }

    public function uploadAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        /** @var \App\Models\User $user **/
        $user = Auth::user();
    
        if ($request->hasFile('avatar')) {
            // Generate the custom filename
            $filename = $user->id . '_' . $user->username . '_' . time() . '.' . $request->avatar->extension();
    
            Log::info("Filename: " . $filename);
    
            // Store the file in the 'public/avatars' directory
            $request->avatar->storeAs('avatars', $filename, 'public');
    
            $oldAvatar = $user->avatar;

            $user->avatar = $filename;
            $user->save();

            if ($oldAvatar != "/fallback-avatar.jpg") {
                Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
            }
    
            return back()->with('success', 'Avatar updated successfully.');
        }
    
        return back();
    }
    
}
  