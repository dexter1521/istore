<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $roles = Role::orderBy('name')->get();
        return view('admin.users.index', compact('roles', 'role'));
    }


    public function data(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value');
        $role = $request->input('role');

        $columns = [
            0 => 'users.id',
            1 => 'users.name',
            2 => 'users.email',
            3 => 'role',
            4 => 'users.created_at',
        ];

        $baseQuery = User::query();
        if (!empty($role)) {
            $baseQuery->role($role);
        }

        $recordsTotal = (clone $baseQuery)->count();

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderCol = $columns[$orderIndex] ?? 'users.id';

        if ($orderCol !== 'role') {
            $baseQuery->orderBy($orderCol, $orderDir);
        }

        $rows = $baseQuery->with('roles')->skip($start)->take($length)->get();

        $data = $rows->map(function ($user) {
            return [
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'rol' => $user->roles->first()?->name ?? '-',
                'fecha' => $user->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([

            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $currentRole = $user->roles->first()?->name;
        return view('admin.users.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        if (array_key_exists('role', $validated)) {
            if (!empty($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            } else {
                $user->syncRoles([]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function resetPassword(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes resetear tu propio password.');
        }

        $tempPassword = Str::random(12);
        $user->update([
            'password' => Hash::make($tempPassword),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password temporal para ' . $user->email . ': ' . $tempPassword);
    }
}
