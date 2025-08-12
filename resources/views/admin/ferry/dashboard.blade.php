@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Ferry Operations Dashboard</h1>
    <div class="flex gap-3">
      <a href="{{ route('admin.ferry.passengers') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Passenger Lists
      </a>
      <a href="{{ route('admin.ferry.schedule') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        Schedule
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
        <div class="text-xs {{ $stats['alerts_count'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-500 dark:text-slate-400' }}">
          {{ $stats['alerts_count'] > 0 ? 'requires attention' : 'all clear' }}
        </div>
      </div>
      <div class="w-12 h-12 rounded-lg {{ $stats['alerts_count'] > 0 ? 'bg-red-100 dark:bg-red-900/50' : 'bg-slate-100 dark:bg-slate-800' }} grid place-content-center text-2xl">
        {{ $stats['alerts_count'] > 0 ? '⚠️' : '✅' }}
      </div>
    </div>
  </div>
</div>

<!-- Today's Schedule -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Today's Schedule</h2>
      <div class="text-sm text-slate-500 dark:text-slate-400">{{ date('F j, Y') }}</div>
    </div>
  </div>
  <div class="overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Departure</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Route</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Capacity</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booked</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($todayTrips as $trip)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ date('g:i A', strtotime($trip->departure_time)) }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ date('M j', strtotime($trip->departure_date)) }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $trip->departure_location }} → {{ $trip->arrival_location }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $trip->duration_hours }}h journey</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              {{ $trip->passenger_capacity }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $trip->booked_passengers }}</div>
                <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                  <div class="bg-{{ $trip->utilization_color }}-500 h-2 rounded-full" style="width: {{ $trip->utilization_percentage }}%"></div>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $trip->utilization_percentage }}%</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $trip->status === 'scheduled' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                {{ $trip->status === 'boarding' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300' : '' }}
                {{ $trip->status === 'departed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                {{ $trip->status === 'canceled' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}">
                {{ ucfirst($trip->status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="flex justify-center gap-2">
                <a href="{{ route('admin.ferry.passengers', $trip->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                  View Passengers
                </a>
                @if($trip->status === 'scheduled')
                  <button onclick="updateTripStatus({{ $trip->id }}, 'boarding')" class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium">
                    Start Boarding
                  </button>
                @elseif($trip->status === 'boarding')
                  <button onclick="updateTripStatus({{ $trip->id }}, 'departed')" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium">
                    Depart
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
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

<!-- Charts Section -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
  <!-- Passenger Volume Chart -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Passenger Volume (Last 7 Days)</h2>
    </div>
    <div class="p-6">
      <canvas id="passengerChart" height="200"></canvas>
    </div>
  </div>

  <!-- Route Performance -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Route Performance</h2>
    </div>
    <div class="p-6">
      @foreach($routePerformance as $route)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-slate-200 dark:border-slate-700' : '' }}">
          <div>
            <div class="font-medium text-slate-900 dark:text-slate-100">{{ $route->route_name }}</div>
            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $route->trip_count }} trips</div>
          </div>
          <div class="text-right">
            <div class="font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($route->revenue, 2) }}</div>
            <div class="text-sm text-slate-500 dark:text-slate-400">{{ number_format($route->avg_utilization, 1) }}% avg</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Recent Alerts & Issues -->
@if($recentAlerts->count() > 0)
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
      <span class="text-red-500">⚠️</span>
      Recent Alerts & Issues
    </h2>
  </div>
  <div class="p-6">
    @foreach($recentAlerts as $alert)
      <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-200 dark:border-slate-700' : '' }}">
        <div class="w-2 h-2 rounded-full bg-red-500 mt-2 flex-shrink-0"></div>
        <div class="flex-1">
          <div class="font-medium text-slate-900 dark:text-slate-100">{{ $alert->title }}</div>
          <div class="text-sm text-slate-600 dark:text-slate-400">{{ $alert->description }}</div>
          <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $alert->created_at->diffForHumans() }}</div>
        </div>
        <button onclick="dismissAlert({{ $alert->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          ✕
        </button>
      </div>
    @endforeach
  </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  // Passenger Volume Chart
  const passengerData = @json($passengerChartData);
  const passengerCtx = document.getElementById('passengerChart').getContext('2d');
  new Chart(passengerCtx, {
    type: 'bar',
    data: {
      labels: passengerData.map(d => d.date),
      datasets: [{
        label: 'Passengers',
        data: passengerData.map(d => d.passengers),
        backgroundColor: 'rgba(59, 130, 246, 0.8)',
        borderColor: '#3b82f6',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });

  // Trip status update
  function updateTripStatus(tripId, status) {
    if (confirm(`Are you sure you want to mark this trip as ${status}?`)) {
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
  }

  // Dismiss alert
  function dismissAlert(alertId) {
    fetch(`/admin/ferry/alerts/${alertId}/dismiss`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(() => location.reload());
  }

  // Auto-refresh every 5 minutes
  setTimeout(() => location.reload(), 300000);
</script>
@endsection
