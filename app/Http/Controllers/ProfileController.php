<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
    {

        $user = $request->user();
        $user->fill($request->validated());

        // Si el email fue modificado, desverificarlo
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Guardar la ruta de la imagen anterior
        $oldProfileImage = $user->getOriginal('profile_image');

        // Manejar la subida de la imagen de perfil
        if ($request->hasFile('profile_image')) {
            // Guardar la nueva imagen
            if (env('APP_ENV') === 'production') {
                $path = $request->file('profile_image')->store('imgs/profile_images', 'custom');
            } else {
                $path = $request->file('profile_image')->store('imgs/profile_images', 'public');
            }

            // Actualizar el campo profile_image del usuario
            $user->profile_image = $path;

            // Eliminar la imagen anterior si existe
            if ($oldProfileImage) {
                // Registrar la ruta para depuración
                Log::info('Old profile image path from DB:', ['path' => $oldProfileImage]);
                Log::info('Attempting to delete image:', ['path' => $oldProfileImage]);

                // Eliminar el archivo de acuerdo al entorno
                if (env('APP_ENV') === 'production') {
                    Storage::disk('custom')->delete($oldProfileImage);
                } else {
                    Storage::disk('public')->delete($oldProfileImage);
                }
            }
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
