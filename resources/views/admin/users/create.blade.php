@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 grid place-content-center text-xl">👤</div>
        <div>
          <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Create New User</h1>
          <p class="text-sm text-slate-600 dark:text-slate-400">Add a new user to the system</p>
        </div>
      </div>
    </div>
    
    <div class="p-6">
      <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf
        
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')
              <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
              <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
          </div>
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
          <input type="password" id="password" name="password" required
                 class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          @error('password')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Roles</label>
          <div class="grid gap-3 md:grid-cols-2">
            @foreach($roles as $role)
              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                       class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <div>
                  <div class="font-medium text-slate-900 dark:text-slate-100">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">
                    @if($role->name === 'admin')
                      Full system access and management
                    @elseif($role->name === 'hotel_manager')
                      Hotel and booking management
                    @elseif($role->name === 'ferry_staff')
                      Ferry operations and ticketing
                    @elseif($role->name === 'theme_staff')
                      Theme park management
                    @else
                      Standard user permissions
                    @endif
                  </div>
                </div>
              </label>
            @endforeach
          </div>
          @error('roles')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
        </div>
        
        <div class="flex items-center gap-3 pt-6 border-t border-slate-200 dark:border-slate-600">
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Create User
          </button>
          <a href="{{ route('admin.users.index') }}" class="bg-slate-300 hover:bg-slate-400 text-slate-700 px-6 py-2 rounded-lg font-medium transition-colors">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
