@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Ferry Schedule Management</h1>
    <div class="flex gap-3">
      <a href="{{ route('manage.ferry.dashboard') }}" class="group relative bg-gradient-to-r from-slate-600 to-slate-700 hover:from-slate-700 hover:to-slate-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">📊</span>
        <span>Dashboard</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <button onclick="openAddTripModal()" class="group relative bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">➕</span>
        <span>Add New Trip</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </button>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-8">
  <form method="GET" class="flex flex-wrap gap-4 items-end">
    <div>
      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date Range</label>
      <div class="flex gap-2">
        <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d', strtotime('+30 days'))) }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Route</label>
      <select name="route" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        <option value="">All Routes</option>
        @foreach($routes as $route)
          <option value="{{ $route }}" {{ request('route') === $route ? 'selected' : '' }}>{{ $route }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
      <select name="status" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        <option value="">All Status</option>
        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
        <option value="boarding" {{ request('status') === 'boarding' ? 'selected' : '' }}>Boarding</option>
        <option value="departed" {{ request('status') === 'departed' ? 'selected' : '' }}>Departed</option>
        <option value="arrived" {{ request('status') === 'arrived' ? 'selected' : '' }}>Arrived</option>
        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
      </select>
    </div>

    <button type="submit" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
      <span class="text-lg">🔍</span>
      <span>Apply Filters</span>
      <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </button>
    
    <button type="button" onclick="window.location.href = window.location.pathname" class="group relative bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700">
      <span class="text-lg">🔄</span>
      <span>Clear</span>
    </button>
  </form>
</div>

<!-- Schedule Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Ferry Schedule</h2>
      <div class="text-sm text-slate-500 dark:text-slate-400">{{ $trips->total() }} total trips</div>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trip ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date & Time</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Route</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passengers</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Price</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($trips as $trip)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900 dark:text-slate-100">
              #{{ str_pad($trip->id, 6, '0', STR_PAD_LEFT) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                {{ date('M j, Y', strtotime($trip->date)) }}
              </div>
              <div class="text-sm text-slate-500 dark:text-slate-400">
                {{ date('g:i A', strtotime($trip->depart_time)) }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $trip->origin }} → {{ $trip->destination }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ $trip->trip_type }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              @php
                $bookedPassengers = $trip->tickets->where('status', 'paid')->sum('quantity');
                $utilizationPercentage = $trip->capacity > 0 ? round(($bookedPassengers / $trip->capacity) * 100, 1) : 0;
              @endphp
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $bookedPassengers }}/{{ $trip->capacity }}</div>
              <div class="text-xs text-{{ $utilizationPercentage >= 80 ? 'red' : ($utilizationPercentage >= 60 ? 'amber' : 'green') }}-600 dark:text-{{ $utilizationPercentage >= 80 ? 'red' : ($utilizationPercentage >= 60 ? 'amber' : 'green') }}-400">
                {{ $utilizationPercentage }}% full
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
              ${{ number_format($trip->price, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($trip->status === 'scheduled') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                @elseif($trip->status === 'boarding') bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400
                @elseif($trip->status === 'departed') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400
                @elseif($trip->status === 'completed') bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400
                @else bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400 @endif">
                {{ ucfirst($trip->status ?? 'scheduled') }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
              <div class="flex items-center justify-center gap-2">
                <a href="{{ route('manage.ferry.passengers.advanced', $trip->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="View Passengers">
                  👥
                </a>
                <button onclick="editTrip({{ $trip->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Edit Trip">
                  ✏️
                </button>
                @if(!$trip->blocked)
                  <button onclick="updateTripStatus({{ $trip->id }}, 'boarding')" class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300" title="Set to Boarding">
                    🚢
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
                <p>No ferry trips found for the selected criteria</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($trips->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
      {{ $trips->appends(request()->query())->links() }}
    </div>
  @endif
</div>

<!-- Add Trip Modal -->
<div id="addTripModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Add New Ferry Trip</h3>
      </div>
      <form id="addTripForm" class="p-6 space-y-4">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date</label>
            <input type="date" name="date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Departure Time</label>
            <input type="time" name="depart_time" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
          </div>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Origin</label>
            <select name="departure_location" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
              <option value="">Select Origin</option>
              <option value="Male' City">Male' City</option>
              <option value="Picnic Island">Picnic Island</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Destination</label>
            <select name="arrival_location" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
              <option value="">Select Destination</option>
              <option value="Male' City">Male' City</option>
              <option value="Picnic Island">Picnic Island</option>
            </select>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Capacity</label>
            <input type="number" name="capacity" value="50" min="1" max="200" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Price ($)</label>
            <input type="number" name="price" value="15.00" step="0.01" min="0" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Trip Type</label>
          <select name="trip_type" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
            <option value="departure">Departure (Male' City → Picnic Island)</option>
            <option value="return">Return (Picnic Island → Male' City)</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
          <select name="status" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
            <option value="scheduled">Scheduled</option>
            <option value="boarding">Boarding</option>
            <option value="departed">Departed</option>
            <option value="completed">Completed</option>
            <option value="canceled">Canceled</option>
          </select>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" onclick="closeAddTripModal()" class="px-6 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700">
            Cancel
          </button>
          <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-2 rounded-lg font-medium">
            Add Trip
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openAddTripModal() {
  document.getElementById('addTripModal').classList.remove('hidden');
}

function closeAddTripModal() {
  document.getElementById('addTripModal').classList.add('hidden');
  document.getElementById('addTripForm').reset();
}

function editTrip(tripId) {
  // Implement edit functionality
  alert('Edit trip functionality to be implemented');
}

function updateTripStatus(tripId, status) {
  if(confirm(`Are you sure you want to update this trip status to ${status}?`)) {
    // Implement status update
    alert('Status update functionality to be implemented');
  }
}

// Close modal on background click
document.getElementById('addTripModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeAddTripModal();
  }
});
</script>
@endsection
