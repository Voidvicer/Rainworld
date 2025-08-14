@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Admin Dashboard</h1>
    <div class="flex gap-3">
      <a href="{{ route('admin.users.index') }}" class="group relative bg-gradient-to-r from-indigo-600 to-blue-700 hover:from-indigo-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">👥</span>
        <span>Manage Users</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <a href="{{ route('admin.reports') }}" class="group relative bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">📊</span>
        <span>View Reports</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
    </div>
  </div>
</div>

<!-- Main Stats Grid -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
  <div class="rounded-xl p-5 bg-gradient-to-br from-indigo-600 to-purple-700 text-white shadow-lg">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Total Users</div>
    <div class="text-3xl font-bold">{{ number_format($stats['users']) }}</div>
    <div class="text-xs opacity-70 mt-1">{{ number_format($stats['active_users']) }} active</div>
  </div>
  <div class="rounded-xl p-5 bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Hotel Bookings</div>
    <div class="text-3xl font-bold">{{ number_format($stats['hotel_bookings']) }}</div>
    <div class="text-xs opacity-70 mt-1">{{ number_format($stats['today_checkins']) }} check-ins today</div>
  </div>
  <div class="rounded-xl p-5 bg-gradient-to-br from-cyan-500 to-blue-600 text-white shadow-lg">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Ferry Tickets</div>
    <div class="text-3xl font-bold">{{ number_format($stats['ferry_tickets']) }}</div>
    <div class="text-xs opacity-70 mt-1">{{ number_format($stats['today_ferry_passengers']) }} passengers today</div>
  </div>
</div>

<!-- Revenue Overview -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-8">
  <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Total Revenue</h2>
  <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($stats['total_revenue'], 2) }}</div>
  <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">All-time revenue across all services</p>
</div>

<!-- Management Dashboards -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
  <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow group">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">User Management</div>
        <div class="text-lg font-semibold text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400">Manage Users</div>
        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['total_users'] }} total users</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl group-hover:scale-110 transition-transform">👥</div>
    </div>
  </a>

  <a href="{{ route('admin.hotels.dashboard') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow group">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Hotel Management</div>
        <div class="text-lg font-semibold text-slate-900 dark:text-slate-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Hotel Operations</div>
        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['total_bookings'] }} bookings</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl group-hover:scale-110 transition-transform">🏨</div>
    </div>
  </a>

  <a href="{{ route('admin.ferry.dashboard') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow group">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Ferry Operations</div>
        <div class="text-lg font-semibold text-slate-900 dark:text-slate-100 group-hover:text-purple-600 dark:group-hover:text-purple-400">Ferry Management</div>
        <div class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['total_ferry_tickets'] }} tickets sold</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl group-hover:scale-110 transition-transform">⛴️</div>
    </div>
  </a>
</div>
  <!-- Quick System Stats -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">System Overview</h2>
    </div>
    <div class="p-6 space-y-4">
      <div class="flex justify-between items-center">
        <span class="text-slate-600 dark:text-slate-400">Active Hotels</span>
        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $quickStats['hotels_count'] }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-slate-600 dark:text-slate-400">Total Rooms</span>
        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ number_format($quickStats['rooms_count']) }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-slate-600 dark:text-slate-400">Ferry Trips Today</span>
        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $quickStats['ferry_trips_today'] }}</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-slate-600 dark:text-slate-400">Active Promotions</span>
        <span class="font-semibold text-slate-900 dark:text-slate-100">{{ $quickStats['promotions_active'] }}</span>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Recent Activity</h2>
    </div>
    <div class="p-6">
      <div class="space-y-3">
        @forelse($recentActivity as $activity)
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full 
              {{ $activity['type'] === 'hotel_booking' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'bg-cyan-100 dark:bg-cyan-900/50 text-cyan-600 dark:text-cyan-400' }} 
              grid place-content-center text-xs">
              {{ $activity['type'] === 'hotel_booking' ? '🏨' : '⛴️' }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm text-slate-900 dark:text-slate-100 truncate">{{ $activity['description'] }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ $activity['time']->diffForHumans() }}</p>
            </div>
            <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
              ${{ number_format($activity['amount'], 2) }}
            </div>
          </div>
        @empty
          <p class="text-slate-500 dark:text-slate-400 text-center py-4">No recent activity</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- Management Links -->
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <a href="{{ route('admin.users.index') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 grid place-content-center text-xl">👥</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">User Management</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Manage system users & roles</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.ads') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-xl">📢</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Promotions</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Manage advertisements</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.map') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-xl">🗺️</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Map Content</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Manage island locations</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.reports') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-xl">📊</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Analytics</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Detailed system reports</div>
      </div>
    </div>
  </a>
</div>
@endsection
