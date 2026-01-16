<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Affiche la liste des utilisateurs avec formulaire pour changer le rôle
    public function index()
    {
        $users = User::all();
        return view('dashboard', compact('users'));
    }

    // Met à jour le rôle d'un utilisateur
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return redirect()->route('dashboard')->with('status', 'Rôle mis à jour avec succès.');
    }

     public function store(Request $request)
    {
        // Vérifie que c'est un admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Action non autorisée');
        }

        // Validation des champs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
        ]);

        // Création de l'utilisateur
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('dashboard')->with('status', 'Utilisateur créé avec succès.');
    }

    public function destroy(User $user)
{
    // Sécurité : seulement admin
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }

    // Empêcher l’admin de se supprimer lui-même
    if ($user->id === auth()->id()) {
        return redirect()->route('dashboard')
            ->with('status', 'Impossible de supprimer votre propre compte.');
    }

    $user->delete();

    return redirect()->route('dashboard')
        ->with('status', 'Utilisateur supprimé avec succès.');
}
}
