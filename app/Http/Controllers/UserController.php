<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios con sus roles
        $users = User::with('roles')->get();

        // Retornar la vista con la lista de usuarios
        return view('admin.users.users-index', compact('users'));
    }

    

    public function destroy($id)
{
    $user = User::findOrFail($id);

    // Guardar la ruta de la imagen de perfil antes de eliminar al usuario
    $profileImage = $user->profile_image;

    // Eliminar al usuario
    $user->delete();

    // Eliminar la imagen de perfil del usuario si existe
    if ($profileImage) {
        if (env('APP_ENV') === 'production') {
            // Eliminar la imagen del disco 'custom' en producción
            Storage::disk('custom')->delete($profileImage);
        } else {
            // Eliminar la imagen del disco 'public' en otros entornos
            Storage::disk('public')->delete($profileImage);
        }
    }

    return redirect()->route('users')->with('success', 'User deleted successfully.');
}

    public function editRole($userId)
    {
        $user = User::findOrFail($userId);
        $roles = Role::all();
        return view('admin.users.users-edit-role', compact('user', 'roles'));
    }

    public function updateRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Obtener los nombres de los roles seleccionados
        $roles = Role::whereIn('id', $request->input('roles', []))->pluck('name')->toArray();
        
        // Sincronizar roles
        $user->syncRoles($roles);
    
        return redirect()->route('users')->with('success', 'Roles updated successfully.');
    }
}
