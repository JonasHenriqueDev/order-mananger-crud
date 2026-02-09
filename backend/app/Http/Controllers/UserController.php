<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role' => ['required'],
            'active' => ['boolean'],
        ]);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'min:8'],
            'role' => ['sometimes'],
            'active' => ['boolean'],
        ]);

        if (isset($data['password'])) {
            $user->password = $data['password'];
        }

        $user->update($data);

        return $user;
    }

    public function toggle(User $user)
    {
        $user->update([
            'active' => ! $user->active,
        ]);

        return response()->json([
            'active' => $user->active,
        ]);
    }
}
