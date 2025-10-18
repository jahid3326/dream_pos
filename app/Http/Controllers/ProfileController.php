<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Get the currently authenticated user.
        $user = Auth::user();

        // Return the view, passing the user data to it.
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the incoming form data using the single 'name' field.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            // Add validation for phone_number if you have that column on the users table.
        ]);

        // --- Update User Data ---

        // 1. Update name and email.
        $user->name = $request->name;
        $user->email = $request->email;

        // 2. Update password only if a new one was provided.
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 3. Handle profile picture upload.
        if ($request->hasFile('profile_picture')) {
            // Delete the old picture if it exists.
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            // Store the new picture.
            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // 4. Save all changes to the database.
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}
