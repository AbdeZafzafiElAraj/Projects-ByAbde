<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\RegistroAcceso;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Si es admin, obtiene todos los registros, si no, solo los del usuario
        $registros = $user->is_admin 
            ? RegistroAcceso::with(['user', 'parking', 'plaza.planta'])
                ->whereHas('user')
                ->whereHas('parking')
                ->whereHas('plaza.planta')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
            : RegistroAcceso::where('user_id', $user->id)
                ->with(['parking', 'plaza.planta'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

        return view('profile.edit', [
            'user' => $user,
            'registros' => $registros
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

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

    public function accesos(Request $request): View
    {
        $user = $request->user();
        $registros = RegistroAcceso::where('user_id', $user->id)
            ->with(['parking', 'plaza.planta'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.accesos', compact('registros'));
    }
}
