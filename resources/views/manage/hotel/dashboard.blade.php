@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Hotel Management Dashboard</h1>
    <div class="flex gap-3">
      <a href="{{ route('manage.hotel.availability') }}" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">🏨</span>
        <span>Room Availability</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <a href="{{ route('manage.hotel.reports.advanced') }}" class="group relative bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">📊</span>
        <span>Reports</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
    </div>
  </div>
</div>

<!-- Stats Overview -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Hotels</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total_hotels'] }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $stats['active_hotels'] }} active</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">🏨</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Rooms</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['total_rooms']) }}</div>
        <div class="text-xs text-slate-600 dark:text-slate-400">across all hotels</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">🛏️</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Today's Check-ins</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['today_checkins'] }}</div>
        <div class="text-xs text-amber-600 dark:text-amber-400">{{ $stats['today_checkouts'] }} check-outs</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-2xl">📅</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Pending</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['pending_bookings'] }}</div>
        <div class="text-xs text-red-600 dark:text-red-400">requires attention</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/50 grid place-content-center text-2xl">⏳</div>
    </div>
  </div>
</div>

<!-- Occupancy Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Occupancy Trends (Last 7 Days)</h2>
  </div>
  <div class="p-6">
    <canvas id="occupancyChart" height="80"></canvas>
  </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Recent Bookings</h2>
  </div>
  <div class="overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel / Room</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dates</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($recentBookings as $booking)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 grid place-content-center text-white font-semibold text-xs">
                  {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                </div>
                <div class="ml-3">
                  <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $booking->user->name }}</div>
                  <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->user->email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $booking->room->hotel->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->room->name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              {{ date('M j', strtotime($booking->check_in)) }} - {{ date('M j', strtotime($booking->check_out)) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
              ${{ number_format($booking->total_amount, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($booking->status === 'confirmed') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400
                @elseif($booking->status === 'pending') bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400
                @elseif($booking->status === 'canceled') bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400
                @else bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 @endif">
                {{ ucfirst($booking->status) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">📋</div>
                <p>No recent bookings</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const occupancyData = @json($occupancyData);
  
  const ctx = document.getElementById('occupancyChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: occupancyData.map(d => d.date),
      datasets: [{
        label: 'Occupancy Rate',
        data: occupancyData.map(d => d.occupancy_rate),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 4,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        },
        x: {
          ticks: { maxRotation: 0 }
        }
      }
    }
  });
</script>
@endsection
