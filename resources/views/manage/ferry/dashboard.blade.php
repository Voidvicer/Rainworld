@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Ferry Operations Dashboard</h1>
    <div class="flex gap-3">
      <a href="{{ route('manage.ferry.passengers.advanced') }}" class="group relative bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">👥</span>
        <span>Passenger Lists</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <a href="{{ route('manage.ferry-trips.index') }}" class="group relative bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">📅</span>
        <span>Schedule</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
    </div>
  </div>
</div>

<!-- Quick Stats -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Today's Trips</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['today_trips'] }}</div>
        <div class="text-xs text-blue-600 dark:text-blue-400">{{ $stats['active_trips'] }} active</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">⛴️</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Passengers</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['today_passengers'] }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $stats['capacity_utilization'] }}% capacity</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">👥</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Revenue</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($stats['today_revenue'], 2) }}</div>
        <div class="text-xs text-purple-600 dark:text-purple-400">{{ $stats['revenue_change'] }}% vs yesterday</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">💰</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Alerts</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['alerts_count'] }}</div>
        <div class="text-xs text-{{ $stats['alerts_count'] > 0 ? 'red' : 'green' }}-600 dark:text-{{ $stats['alerts_count'] > 0 ? 'red' : 'green' }}-400">
          {{ $stats['alerts_count'] > 0 ? 'Requires attention' : 'All clear' }}
        </div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-{{ $stats['alerts_count'] > 0 ? 'red' : 'green' }}-100 dark:bg-{{ $stats['alerts_count'] > 0 ? 'red' : 'green' }}-900/50 grid place-content-center text-2xl">
        {{ $stats['alerts_count'] > 0 ? '⚠️' : '✅' }}
      </div>
    </div>
  </div>
</div>

<!-- Today's Ferry Trips -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Today's Ferry Schedule</h2>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Route</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passengers</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Capacity</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($todayTrips as $trip)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-slate-100">
              {{ date('g:i A', strtotime($trip->depart_time)) }}
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $trip->origin }} → {{ $trip->destination }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $trip->trip_type }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-900 dark:text-slate-100">
              {{ $trip->booked_passengers }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $trip->capacity }}</div>
              <div class="text-xs text-{{ $trip->utilization_color }}-600 dark:text-{{ $trip->utilization_color }}-400">
                {{ $trip->utilization_percentage }}% full
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($trip->status === 'scheduled') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                @elseif($trip->status === 'boarding') bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400
                @elseif($trip->status === 'departed') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400
                @elseif($trip->status === 'completed') bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400
                @else bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400 @endif">
                {{ ucfirst($trip->status) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
                <p>No trips scheduled for today</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Quick Actions -->
<div class="grid gap-4 sm:grid-cols-3">
  <a href="{{ route('manage.ferry.validate.form') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/50 grid place-content-center text-xl">✅</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Validate Tickets</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Check passenger tickets</div>
      </div>
    </div>
  </a>

  <a href="{{ route('manage.ferry.reports') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">📊</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Reports</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Trip and revenue reports</div>
      </div>
    </div>
  </a>

  <a href="{{ route('manage.ferry-trips.index') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-xl">⚙️</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Manage Trips</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Add, edit ferry trips</div>
      </div>
    </div>
  </a>
</div>
@endsection
