<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {   $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sport' => 'nullable|string|max:255',
            'federation' => 'nullable|string|max:255',
            'competitionLevel' => 'nullable|in:Olympique,Professionnel,Amateur,Autre',
            'allergies' => 'nullable|array',
        ]);

        $user->Name = $validated['name'];
        $user->sport = $validated['sport'] ?? null;
        $user->federation = $validated['federation'] ?? null;
        $user->competitionLevel = $validated['competitionLevel'] ?? null;
        $user->allergies = isset($validated['allergies']) ? json_encode($validated['allergies']) : null;

        if (isset($validated['email']) && $user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
