@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">🏨</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Hotel Room Availability</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Real-time room availability across all hotels</p>
          </div>
        </div>
        <a href="{{ route('admin.hotels.dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Hotel Dashboard
        </a>
      </div>
    </div>
    
    <div class="p-6">
      <!-- Date Selector -->
      <form method="GET" class="mb-6">
        <div class="flex items-center gap-4">
          <div>
            <label for="date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Check Date</label>
            <input type="date" id="date" name="date" value="{{ $selectedDate }}" 
                   class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Check Availability
            </button>
          </div>
        </div>
      </form>
      
      <!-- Availability Overview -->
      <div class="space-y-6">
        @forelse($hotels as $hotel)
          <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $hotel->name }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $hotel->address ?? 'No address provided' }}</p>
              </div>
              <div class="text-right">
                @php
                  $totalRooms = $hotel->rooms->sum('total_rooms');
                  $occupiedRooms = $hotel->rooms->sum('occupied_rooms');
                  $availableRooms = $totalRooms - $occupiedRooms;
                  $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
                @endphp
                <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $availableRooms }}/{{ $totalRooms }}</div>
                <div class="text-sm text-slate-600 dark:text-slate-400">rooms available</div>
                <div class="text-xs {{ $occupancyRate > 80 ? 'text-red-600 dark:text-red-400' : ($occupancyRate > 60 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400') }}">
                  {{ $occupancyRate }}% occupied
                </div>
              </div>
            </div>
            
            <!-- Room Type Breakdown -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
              @foreach($hotel->rooms as $room)
                @php
                  $roomAvailable = $room->total_rooms - $room->occupied_rooms;
                  $roomOccupancyRate = $room->total_rooms > 0 ? round(($room->occupied_rooms / $room->total_rooms) * 100, 1) : 0;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-4">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-slate-900 dark:text-slate-100">{{ $room->name }}</h4>
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                      {{ $roomAvailable === 0 ? 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' : 
                         ($roomAvailable <= 2 ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400' : 
                          'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400') }}">
                      {{ $roomAvailable === 0 ? 'Full' : ($roomAvailable <= 2 ? 'Low' : 'Available') }}
                    </span>
                  </div>
                  
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                      <span class="text-slate-600 dark:text-slate-400">Type:</span>
                      <span class="text-slate-900 dark:text-slate-100">{{ ucfirst($room->type) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-slate-600 dark:text-slate-400">Capacity:</span>
                      <span class="text-slate-900 dark:text-slate-100">{{ $room->capacity }} guests</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-slate-600 dark:text-slate-400">Available:</span>
                      <span class="font-medium text-slate-900 dark:text-slate-100">{{ $roomAvailable }}/{{ $room->total_rooms }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-slate-600 dark:text-slate-400">Price:</span>
                      <span class="font-medium text-emerald-600 dark:text-emerald-400">${{ number_format($room->price_per_night, 2) }}/night</span>
                    </div>
                  </div>
                  
                  <!-- Occupancy Bar -->
                  <div class="mt-3">
                    <div class="flex justify-between text-xs text-slate-600 dark:text-slate-400 mb-1">
                      <span>Occupancy</span>
                      <span>{{ $roomOccupancyRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                      <div class="h-2 rounded-full transition-all duration-300
                        {{ $roomOccupancyRate > 80 ? 'bg-red-500' : ($roomOccupancyRate > 60 ? 'bg-amber-500' : 'bg-green-500') }}"
                        style="width: {{ $roomOccupancyRate }}%"></div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @empty
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl mx-auto mb-4">🏨</div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No active hotels found</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Hotels will appear here once activated</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="grid gap-4 sm:grid-cols-3">
  <a href="{{ route('admin.hotels.reports') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">📊</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Booking Reports</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Detailed booking analytics</div>
      </div>
    </div>
  </a>

  <a href="{{ route('admin.hotels.promotions') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-xl">🎯</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Promotions</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Manage hotel promotions</div>
      </div>
    </div>
  </a>

  <a href="{{ route('manage.bookings') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-xl">🎫</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Booking Management</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Manage current bookings</div>
      </div>
    </div>
  </a>
</div>
@endsection
