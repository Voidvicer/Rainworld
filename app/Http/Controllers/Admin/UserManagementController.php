<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }
        
        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        $user->assignRole($request->roles);
        
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }
    
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Rules\Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name']
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        
        $user->syncRoles($request->roles);
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
    
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }
        
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
    
    public function toggleStatus(User $user)
    {
        $user->update(['active' => !$user->active]);
        $status = $user->active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
}
