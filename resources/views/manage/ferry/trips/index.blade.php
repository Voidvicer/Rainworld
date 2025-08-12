@extends('layouts.app')
@section('content')

<!-- Header with Navigation Tabs -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">⛴️</div>
        <div>
          <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Ferry Management</h1>
          <p class="text-sm text-slate-600 dark:text-slate-400">Manage ferry schedules and operations</p>
        </div>
      </div>
      
      <!-- Management Navigation Tabs -->
      <div class="flex gap-2">
        <a href="{{ route('manage.ferry-trips.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('manage.ferry-trips.*') ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
          📅 Schedule Management
        </a>
        <a href="{{ route('manage.ferry.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('manage.ferry.dashboard*') ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
          📊 Ferry Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="p-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end mb-6">
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Filter by Date</label>
        <input type="date" name="date" value="{{ $selectedDate }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 w-48" onchange="this.form.submit()">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Trip Type</label>
        <select name="trip_type" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100 w-48" onchange="this.form.submit()">
          <option value="">All Types</option>
          <option value="departure" {{ request('trip_type') === 'departure' ? 'selected' : '' }}>Departure</option>
          <option value="return" {{ request('trip_type') === 'return' ? 'selected' : '' }}>Return</option>
        </select>
      </div>
      <div class="flex gap-3">
        <a href="{{ route('manage.ferry-trips.index') }}" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Clear Filters
        </a>
        <a href="{{ route('manage.ferry-trips.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Add New Trip
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Ferry Trips Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Ferry Schedule</h2>
  </div>
  <div class="overflow-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3 text-left font-semibold">Date</th>
          <th class="px-6 py-3 text-left font-semibold">Trip Type</th>
          <th class="px-6 py-3 text-left font-semibold">Time</th>
          <th class="px-6 py-3 text-left font-semibold">Route</th>
          <th class="px-6 py-3 text-center font-semibold">Capacity</th>
          <th class="px-6 py-3 text-center font-semibold">Sold</th>
          <th class="px-6 py-3 text-center font-semibold">Available</th>
          <th class="px-6 py-3 text-right font-semibold">Price</th>
          <th class="px-6 py-3 text-center font-semibold">Status</th>
          <th class="px-6 py-3 text-center font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white/60 dark:bg-slate-900/20">
        @forelse($trips as $trip)
        <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors group">
          <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
            <span class="font-medium">{{ date('M j, Y', strtotime($trip->date)) }}</span>
            <div class="text-xs text-slate-500">{{ date('l', strtotime($trip->date)) }}</div>
          </td>
          <td class="px-6 py-4">
            @if($trip->trip_type === 'departure')
            <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded text-xs font-semibold">Departure</span>
            @else
            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded text-xs font-semibold">Return</span>
            @endif
          </td>
          <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
            <span class="font-mono bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ date('H:i', strtotime($trip->depart_time)) }}</span>
          </td>
          <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
            <div class="flex items-center gap-2">
              <span class="font-medium">{{ $trip->origin }}</span>
              <span class="text-slate-400 dark:text-slate-500">→</span>
              <span class="font-medium">{{ $trip->destination }}</span>
            </div>
          </td>
          <td class="px-6 py-4 text-center font-medium text-slate-700 dark:text-slate-300">{{ $trip->capacity }}</td>
          <td class="px-6 py-4 text-center">
            @php
            $sold = $trip->tickets()->where('status', 'paid')->sum('quantity');
            @endphp
            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $sold }}</span>
          </td>
          <td class="px-6 py-4 text-center">
            @php
            $remaining = $trip->remainingSeats();
            @endphp
            <span class="font-semibold {{ $remaining > 10 ? 'text-emerald-600 dark:text-emerald-400' : ($remaining > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
              {{ $remaining }}
            </span>
          </td>
          <td class="px-6 py-4 text-right font-bold text-lg text-slate-800 dark:text-slate-200">${{ number_format($trip->price, 2) }}</td>
          <td class="px-6 py-4 text-center">
            @if($trip->blocked)
            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400 rounded text-xs font-semibold">Blocked</span>
            @else
            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded text-xs font-semibold">Active</span>
            @endif
          </td>
          <td class="px-6 py-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <a href="{{ route('manage.ferry-trips.edit', $trip) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded text-xs font-medium hover:bg-blue-200 dark:hover:bg-blue-900/70 transition">
                Edit
              </a>
              <form method="POST" action="{{ route('manage.ferry-trips.destroy', $trip) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this trip?')">@csrf @method('DELETE')
                <button class="inline-flex items-center gap-1 px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-medium transition shadow-sm">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
            <div class="flex flex-col items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
              <div>
                <p class="font-medium">No ferry trips found</p>
                <p class="text-sm text-slate-400 dark:text-slate-500">
                  @if($selectedDate || request('trip_type'))
                    Try adjusting your filters or add a new trip
                  @else
                    Start by adding your first ferry trip
                  @endif
                </p>
              </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  @if($trips->hasPages())
  <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600">
    {{ $trips->links() }}
  </div>
  @endif
</div>
@endsection
