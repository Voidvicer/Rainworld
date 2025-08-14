@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 grid place-content-center text-xl">👥</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">User Management</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Manage system users and their roles</p>
          </div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Add New User
        </a>
      </div>
    </div>
    
    <div class="p-6">
      <!-- Filters -->
      <form method="GET" class="mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
          <input type="text" name="search" value="{{ request('search') }}" 
                 placeholder="Search by name or email..." 
                 class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <select name="role" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Roles</option>
            @foreach($roles as $role)
              <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
              </option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Filter
        </button>
        @if(request()->hasAny(['search', 'role']))
          <a href="{{ route('admin.users.index') }}" class="bg-slate-400 hover:bg-slate-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Clear
          </a>
        @endif
      </form>
      
      <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Roles</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Created</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($users as $user)
              <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 grid place-content-center text-white font-semibold text-sm">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</div>
                      <div class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex flex-wrap gap-1">
                    @forelse($user->roles as $role)
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($role->name === 'admin') bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400
                        @elseif($role->name === 'hotel_manager') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                        @elseif($role->name === 'ferry_staff') bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400
                        @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-400 @endif">
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                      </span>
                    @empty
                      <span class="text-xs text-slate-400 dark:text-slate-500">No roles assigned</span>
                    @endforelse
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $user->active ?? true ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                    {{ $user->active ?? true ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                  {{ $user->created_at->format('M j, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="group relative bg-gradient-to-r from-indigo-600 to-blue-700 hover:from-indigo-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center gap-1">
                      <span>✏️</span>
                      <span>Edit</span>
                      <div class="absolute inset-0 bg-white/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    @if($user->id !== auth()->id())
                      <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" 
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors">
                          Delete
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                  <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">👥</div>
                    <div>
                      <p class="font-medium">No users found</p>
                      <p class="text-xs">Users will appear here once created</p>
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="mt-6">
        {{ $users->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
