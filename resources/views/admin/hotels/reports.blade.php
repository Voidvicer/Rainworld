@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Hotel Reports</h1>
    <div class="flex gap-3">
      <a href="{{ route('admin.hotels.dashboard') }}" class="group relative bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">🏨</span>
        <span>Dashboard</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <button onclick="generateReport()" class="group relative bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
        <span class="text-lg">📊</span>
        <span>Generate Report</span>
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
      <select name="period" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        <option value="7d" {{ request('period') === '7d' ? 'selected' : '' }}>Last 7 Days</option>
        <option value="30d" {{ request('period') === '30d' ? 'selected' : '' }}>Last 30 Days</option>
        <option value="90d" {{ request('period') === '90d' ? 'selected' : '' }}>Last 90 Days</option>
        <option value="1y" {{ request('period') === '1y' ? 'selected' : '' }}>Last Year</option>
        <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom</option>
      </select>
    </div>
    
    <div id="customDateRange" class="flex gap-2" style="display: {{ request('period') === 'custom' ? 'flex' : 'none' }};">
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">From</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">To</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hotel</label>
      <select name="hotel_id" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100">
        <option value="">All Hotels</option>
        @foreach($hotels as $hotel)
          <option value="{{ $hotel->id }}" {{ request('hotel_id') == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
        @endforeach
      </select>
    </div>

    <button type="submit" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
      <span class="text-lg">🔍</span>
      <span>Apply Filters</span>
      <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </button>
  </form>
</div>

<!-- Revenue Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Revenue Overview</h2>
  </div>
  <div class="p-6">
    <canvas id="revenueChart" height="80"></canvas>
  </div>
</div>

<!-- Key Metrics -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Revenue</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($metrics['total_revenue'], 2) }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $metrics['revenue_change'] }}% from last period</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">💰</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Avg Occupancy</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($metrics['avg_occupancy'], 1) }}%</div>
        <div class="text-xs text-blue-600 dark:text-blue-400">{{ $metrics['occupancy_change'] }}% from last period</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">📊</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Bookings</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($metrics['total_bookings']) }}</div>
        <div class="text-xs text-purple-600 dark:text-purple-400">{{ $metrics['bookings_change'] }}% from last period</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">📅</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Avg Daily Rate</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($metrics['avg_daily_rate'], 2) }}</div>
        <div class="text-xs text-amber-600 dark:text-amber-400">{{ $metrics['adr_change'] }}% from last period</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-2xl">🏷️</div>
    </div>
  </div>
</div>

<!-- Top Performing Hotels -->
<div class="grid lg:grid-cols-2 gap-8 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Top Performing Hotels</h2>
    </div>
    <div class="p-6">
      @foreach($topHotels as $hotel)
        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-slate-200 dark:border-slate-700' : '' }}">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 grid place-content-center text-white font-semibold text-xs">
              {{ $loop->iteration }}
            </div>
            <div>
              <div class="font-medium text-slate-900 dark:text-slate-100">{{ $hotel->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $hotel->location }}</div>
            </div>
          </div>
          <div class="text-right">
            <div class="font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($hotel->revenue, 2) }}</div>
            <div class="text-sm text-slate-500 dark:text-slate-400">{{ number_format($hotel->occupancy_rate, 1) }}% occupancy</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Room Type Performance -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Room Type Performance</h2>
    </div>
    <div class="p-6">
      <canvas id="roomTypeChart" height="200"></canvas>
    </div>
  </div>
</div>

<!-- Detailed Booking Report -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Detailed Booking Report</h2>
      <button onclick="exportToCSV()" class="bg-slate-600 hover:bg-slate-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
        Export CSV
      </button>
    </div>
  </div>
  <div class="overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booking ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dates</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Revenue</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($detailedBookings as $booking)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-slate-900 dark:text-slate-100">
              #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $booking->user->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->user->email }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $booking->room->hotel->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->room->name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              {{ date('M j, Y', strtotime($booking->check_in)) }} - {{ date('M j, Y', strtotime($booking->check_out)) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
              ${{ number_format($booking->total_amount, 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $booking->status === 'confirmed' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                {{ $booking->status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300' : '' }}
                {{ $booking->status === 'canceled' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}">
                {{ ucfirst($booking->status) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">📊</div>
                <p>No bookings found for the selected period</p>
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
  // Revenue Chart
  const revenueData = @json($revenueChartData);
  const revenueCtx = document.getElementById('revenueChart').getContext('2d');
  new Chart(revenueCtx, {
    type: 'line',
    data: {
      labels: revenueData.map(d => d.date),
      datasets: [{
        label: 'Revenue',
        data: revenueData.map(d => d.revenue),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '$' + value.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Room Type Chart
  const roomTypeData = @json($roomTypeData);
  const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
  new Chart(roomTypeCtx, {
    type: 'doughnut',
    data: {
      labels: roomTypeData.map(d => d.type),
      datasets: [{
        data: roomTypeData.map(d => d.revenue),
        backgroundColor: [
          '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'
        ]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  // Date range toggle
  document.querySelector('select[name="period"]').addEventListener('change', function() {
    const customRange = document.getElementById('customDateRange');
    customRange.style.display = this.value === 'custom' ? 'flex' : 'none';
  });

  function generateReport() {
    window.print();
  }

  function exportToCSV() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("admin.hotels.reports") }}?' + params.toString();
  }
</script>
@endsection
