@extends('layouts.app')
@section('content')
@php
$currentStatus = $currentStatus ?? null;
@endphp

<!-- Header with Navigation Tabs -->
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-xl">🏨</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Hotel Management</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Manage hotel reservations and operations</p>
          </div>
        </div>
        
        <!-- Management Navigation Tabs -->
        <div class="flex gap-2">
          <a href="{{ route('manage.bookings') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('manage.bookings') ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
            📋 Bookings Management
          </a>
          <a href="{{ route('manage.hotel.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('manage.hotel.*') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
            📊 Hotel Dashboard
          </a>
        </div>
      </div>
    </div>
    
    <div class="p-6">
      <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('manage.bookings') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ !$currentStatus ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">All Bookings</a>
        <a href="{{ route('manage.bookings', ['status' => 'pending']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus === 'pending' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Pending</a>
        <a href="{{ route('manage.bookings', ['status' => 'confirmed']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus === 'confirmed' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Confirmed</a>
        <a href="{{ route('manage.bookings', ['status' => 'completed']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus === 'completed' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Completed</a>
        <a href="{{ route('manage.bookings', ['status' => 'canceled']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $currentStatus === 'canceled' ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Canceled</a>
      </div>
      
      <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel / Room</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dates</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($bookings as $b)
              <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="font-mono text-xs font-medium text-slate-900 dark:text-slate-100">{{ $b->confirmation_code }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100">{{ $b->user->email }}</td>
                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">{{ $b->room->hotel->name }} / {{ $b->room->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-100">{{ $b->check_in }} → {{ $b->check_out }}</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($b->status==='confirmed') bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400
                    @elseif($b->status==='canceled') bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400
                    @elseif($b->status==='completed') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                    @else bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 @endif">
                    {{ ucfirst($b->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <form method="POST" action="{{ route('manage.bookings.status',$b) }}" class="flex items-center justify-center gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="px-3 py-1 text-xs border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none" style="color-scheme: light dark;">
                      @foreach(['pending','confirmed','canceled','completed'] as $s)
                        <option value="{{ $s }}" @if($b->status==$s) selected @endif>{{ ucfirst($s) }}</option>
                      @endforeach
                    </select>
                    <button class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-medium transition shadow-sm">Save</button>
                  </form>
                </td>
              </tr>
            @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">🏨</div>
                  <div>
                    <p class="font-medium">No hotel bookings found</p>
                    <p class="text-xs">Hotel reservations will appear here once created</p>
                  </div>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="mt-6">
        {{ $bookings->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
