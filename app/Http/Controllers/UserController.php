<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return User::orderBy('id')->paginate(20);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['sometimes', Rule::enum(Role::class)],
        ]);

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->role = $data['role'] ?? Role::User;
        $user->save();

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return $user;
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        // Nunca $request->all(): solo se aceptan estos campos
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'required', 'string', Password::defaults()],
            'role' => ['sometimes', Rule::enum(Role::class)],
        ]);

        // Cambiar la contraseña o el email de la propia cuenta exige la contraseña actual,
        // igual que en /profile: una sesión robada no basta para quedarse con la cuenta
        if ($request->user()->is($user) && ($request->has('password') || $request->has('email'))) {
            $request->validate(['current_password' => ['required', 'current_password']]);
        }

        if (array_key_exists('role', $data)) {
            Gate::authorize('changeRole', $user);
            $user->role = $data['role'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->fill(array_intersect_key($data, array_flip(['name', 'email'])));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return $user;
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
