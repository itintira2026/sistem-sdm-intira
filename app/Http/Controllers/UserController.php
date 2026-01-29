<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // public function index()
    // {
    //     $users = User::with('roles')->latest()->paginate(10);
    //     $roles = Role::all();

    //     return view('management_data.user.index', compact('users', 'roles'));
    // }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $role    = $request->get('role');
        $search  = $request->get('search');

        $users = User::with(['roles', 'branchAssignments.branch'])
            ->when($role, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString(); // 🔥 penting

        $roles = Role::all();

        return view('management_data.user.index', compact(
            'users',
            'roles',
            'perPage',
            'role',
            'search'
        ));
    }

    public function create()
    {
        $roles = Role::all();
        $branches = \App\Models\Branch::all();

        return view('management_data.user.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'branch']);

        return view('management_data.user.show', compact('user'));
    }

 public function edit(User $user)
    {
        $roles = Role::all();
        $branches = Branch::all();
        $user->load(['roles', 'branches']);

        return view('management_data.user.edit', compact('user', 'roles', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'nullable|boolean',
            'branch_managers' => 'nullable|array',
            'branch_managers.*' => 'boolean',
        ]);

        // Update basic info
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->is_active = $request->has('is_active') ? true : false;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->save();

        // Update role using Spatie
        $user->syncRoles([$validated['role']]);

        // Sync branches with manager status
        // First, remove all existing branch assignments
        $user->branchAssignments()->delete();

        // Then, add new assignments
        foreach ($validated['branches'] as $branchId) {
            $isManager = isset($request->branch_managers[$branchId]) && $request->branch_managers[$branchId];
            $user->assignToBranch($branchId, $isManager);
        }

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diaktifkan');
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dinonaktifkan');
    }
}
