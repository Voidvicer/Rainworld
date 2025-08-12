@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Ferry Schedule Management</h1>
    <div class="flex gap-3">
      <a href="{{ route('admin.ferry.dashboard') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Dashboard
      </a>
      <button onclick="openAddTripModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Add New Trip
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

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      Apply Filters
    </button>
    
    <a href="{{ route('admin.ferry.schedule') }}" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      Clear
    </a>
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
  <div class="overflow-hidden">
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
                {{ date('M j, Y', strtotime($trip->departure_date)) }}
              </div>
              <div class="text-sm text-slate-500 dark:text-slate-400">
                {{ date('g:i A', strtotime($trip->departure_time)) }} - {{ date('g:i A', strtotime($trip->arrival_time)) }}
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $trip->departure_location }} → {{ $trip->arrival_location }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $trip->duration_hours }}h journey</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                  {{ $trip->booked_passengers }}/{{ $trip->passenger_capacity }}
                </div>
                <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                  <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($trip->booked_passengers / $trip->passenger_capacity) * 100 }}%"></div>
                </div>
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-400">
                {{ number_format(($trip->booked_passengers / $trip->passenger_capacity) * 100, 1) }}% full
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
              ${{ number_format($trip->price_per_person, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <select onchange="updateTripStatus({{ $trip->id }}, this.value)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border-0 bg-transparent
                      {{ $trip->status === 'scheduled' ? 'text-blue-700 dark:text-blue-300' : '' }}
                      {{ $trip->status === 'boarding' ? 'text-amber-700 dark:text-amber-300' : '' }}
                      {{ $trip->status === 'departed' ? 'text-green-700 dark:text-green-300' : '' }}
                      {{ $trip->status === 'arrived' ? 'text-purple-700 dark:text-purple-300' : '' }}
                      {{ $trip->status === 'canceled' ? 'text-red-700 dark:text-red-300' : '' }}">
                <option value="scheduled" {{ $trip->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="boarding" {{ $trip->status === 'boarding' ? 'selected' : '' }}>Boarding</option>
                <option value="departed" {{ $trip->status === 'departed' ? 'selected' : '' }}>Departed</option>
                <option value="arrived" {{ $trip->status === 'arrived' ? 'selected' : '' }}>Arrived</option>
                <option value="canceled" {{ $trip->status === 'canceled' ? 'selected' : '' }}>Canceled</option>
              </select>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="flex justify-center gap-2">
                <a href="{{ route('admin.ferry.passengers', $trip->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                  Passengers
                </a>
                <button onclick="editTrip({{ $trip->id }})" class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium">
                  Edit
                </button>
                @if($trip->status === 'scheduled')
                  <button onclick="cancelTrip({{ $trip->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                    Cancel
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
                <p>No trips found</p>
                <button onclick="openAddTripModal()" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                  Add your first trip
                </button>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- Pagination -->
  @if($trips->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
      {{ $trips->links() }}
    </div>
  @endif
</div>

<!-- Add/Edit Trip Modal -->
<div id="tripModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100" id="modalTitle">Add New Trip</h3>
        <button onclick="closeTripModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>
    
    <form id="tripForm" class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Departure Location</label>
          <select name="departure_location" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
            <option value="">Select departure</option>
            <option value="Main Island Port">Main Island Port</option>
            <option value="North Island Harbor">North Island Harbor</option>
            <option value="South Island Terminal">South Island Terminal</option>
            <option value="East Coast Pier">East Coast Pier</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Arrival Location</label>
          <select name="arrival_location" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
            <option value="">Select arrival</option>
            <option value="Main Island Port">Main Island Port</option>
            <option value="North Island Harbor">North Island Harbor</option>
            <option value="South Island Terminal">South Island Terminal</option>
            <option value="East Coast Pier">East Coast Pier</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Departure Date</label>
          <input type="date" name="departure_date" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Departure Time</label>
          <input type="time" name="departure_time" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Duration (hours)</label>
          <input type="number" name="duration_hours" step="0.5" min="0.5" max="24" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Passenger Capacity</label>
          <input type="number" name="passenger_capacity" min="1" max="500" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Price per Person ($)</label>
          <input type="number" name="price_per_person" step="0.01" min="0" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
          <select name="status" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
            <option value="scheduled">Scheduled</option>
            <option value="boarding">Boarding</option>
            <option value="departed">Departed</option>
            <option value="arrived">Arrived</option>
            <option value="canceled">Canceled</option>
          </select>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button type="button" onclick="closeTripModal()" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">
          Cancel
        </button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
          Save Trip
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  let currentTripId = null;

  function openAddTripModal() {
    currentTripId = null;
    document.getElementById('modalTitle').textContent = 'Add New Trip';
    document.getElementById('tripForm').reset();
    document.getElementById('tripModal').classList.remove('hidden');
  }

  function closeTripModal() {
    document.getElementById('tripModal').classList.add('hidden');
  }

  function editTrip(tripId) {
    currentTripId = tripId;
    document.getElementById('modalTitle').textContent = 'Edit Trip';
    
    // Fetch trip data and populate form
    fetch(`/admin/ferry/trips/${tripId}`)
      .then(response => response.json())
      .then(trip => {
        const form = document.getElementById('tripForm');
        form.departure_location.value = trip.departure_location;
        form.arrival_location.value = trip.arrival_location;
        form.departure_date.value = trip.departure_date;
        form.departure_time.value = trip.departure_time;
        form.duration_hours.value = trip.duration_hours;
        form.passenger_capacity.value = trip.passenger_capacity;
        form.price_per_person.value = trip.price_per_person;
        form.status.value = trip.status;
        
        document.getElementById('tripModal').classList.remove('hidden');
      });
  }

  function updateTripStatus(tripId, status) {
    fetch(`/admin/ferry/trips/${tripId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error updating trip status');
      }
    });
  }

  function cancelTrip(tripId) {
    if (confirm('Are you sure you want to cancel this trip? This action cannot be undone.')) {
      updateTripStatus(tripId, 'canceled');
    }
  }

  // Handle form submission
  document.getElementById('tripForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentTripId ? `/admin/ferry/trips/${currentTripId}` : '/admin/ferry/trips';
    const method = currentTripId ? 'PUT' : 'POST';
    
    fetch(url, {
      method: method,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Error saving trip: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      alert('Error saving trip: ' + error.message);
    });
  });

  // Close modal on outside click
  document.getElementById('tripModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeTripModal();
    }
  });
</script>
@endsection
