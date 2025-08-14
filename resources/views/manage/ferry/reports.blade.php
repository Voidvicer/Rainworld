@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">📊</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Ferry Operations Reports</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Comprehensive analytics and trip reports</p>
          </div>
        </div>
        <a href="{{ route('manage.ferry.dashboard') }}" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
          <span class="text-lg">⛴️</span>
          <span>Ferry Dashboard</span>
          <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </a>
      </div>
    </div>
    
    <!-- Filters -->
    <div class="p-6 bg-slate-50 dark:bg-slate-700/50">
      <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-4">
        <div>
          <label for="dateFrom" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">From Date</label>
          <input type="date" id="dateFrom" name="dateFrom" value="{{ $dateFrom }}" 
                 class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label for="dateTo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">To Date</label>
          <input type="date" id="dateTo" name="dateTo" value="{{ $dateTo }}" 
                 class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label for="route" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Route</label>
          <select id="route" name="route" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Routes</option>
            <option value="Male City → Picnic Island" {{ request('route') == 'Male City → Picnic Island' ? 'selected' : '' }}>Male City → Picnic Island</option>
            <option value="Picnic Island → Male City" {{ request('route') == 'Picnic Island → Male City' ? 'selected' : '' }}>Picnic Island → Male City</option>
            <option value="Male City → Airport" {{ request('route') == 'Male City → Airport' ? 'selected' : '' }}>Male City → Airport</option>
            <option value="Airport → Male City" {{ request('route') == 'Airport → Male City' ? 'selected' : '' }}>Airport → Male City</option>
          </select>
        </div>
        <div>
          <button type="submit" class="group relative bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
            <span class="text-lg">🔍</span>
            <span>Generate Report</span>
            <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Quick Stats -->
<div class="grid gap-6 sm:grid-cols-4 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Trips</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['total_trips'] }}</div>
        <div class="text-xs text-blue-600 dark:text-blue-400">{{ date('M j', strtotime($dateFrom)) }} - {{ date('M j, Y', strtotime($dateTo)) }}</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">⛴️</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Passengers</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['total_passengers']) }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $stats['total_tickets'] }} tickets sold</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">👥</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Avg Occupancy</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['avg_occupancy'] }}%</div>
        <div class="text-xs text-purple-600 dark:text-purple-400">across all trips</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">📈</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Revenue</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="text-xs text-amber-600 dark:text-amber-400">ferry tickets</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-2xl">💰</div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="grid gap-6 lg:grid-cols-2 mb-8">
  <!-- Occupancy Trends Chart -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Occupancy Trends</h3>
    <div class="h-64">
      <canvas id="occupancyChart"></canvas>
    </div>
  </div>

  <!-- Route Performance Chart -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Route Performance</h3>
    <div class="h-64">
      <canvas id="routeChart"></canvas>
    </div>
  </div>
</div>

<!-- Revenue Analytics Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-8">
  <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Daily Revenue Analytics</h3>
  <div class="h-80">
    <canvas id="revenueChart"></canvas>
  </div>
</div>

<!-- Export Options -->
<div class="grid gap-6 lg:grid-cols-3 mb-8">
  <a href="{{ route('manage.ferry.export.trips', request()->query()) }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/50 grid place-content-center text-2xl">📊</div>
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Export Trip Data</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Download detailed trip reports as CSV</p>
      </div>
    </div>
  </a>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">📈</div>
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Performance Score</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['avg_occupancy'] }}% avg occupancy</p>
      </div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">🎯</div>
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Peak Hours</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">Most popular: {{ $stats['peak_time'] ?? 'Morning' }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Detailed Trip Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Detailed Trip Reports</h2>
    <p class="text-sm text-slate-600 dark:text-slate-400">Complete breakdown of ferry operations</p>
  </div>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
      <thead class="bg-slate-50 dark:bg-slate-700">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date & Time</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Route</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Capacity</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booked</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Occupancy</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Revenue</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
        @forelse($trips as $trip)
          @php
            $bookedSeats = $trip->tickets->where('status', 'paid')->sum('quantity');
            $revenue = $trip->tickets->where('status', 'paid')->sum('total_amount');
            $occupancy = $trip->capacity > 0 ? round(($bookedSeats / $trip->capacity) * 100, 1) : 0;
          @endphp
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ date('M j, Y', strtotime($trip->date)) }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ date('g:i A', strtotime($trip->depart_time)) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $trip->origin }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">→ {{ $trip->destination }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400">
                {{ $trip->capacity }} seats
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400">
                {{ $bookedSeats }} pax
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <div class="flex items-center justify-center">
                <div class="w-20 bg-slate-200 dark:bg-slate-600 rounded-full h-2 mr-2">
                  <div class="h-2 rounded-full {{ $occupancy >= 80 ? 'bg-red-500' : ($occupancy >= 60 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                       style="width: {{ min($occupancy, 100) }}%"></div>
                </div>
                <span class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $occupancy }}%</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-slate-900 dark:text-slate-100">
              ${{ number_format($revenue, 2) }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
                <p class="font-medium">No trips found for the selected period</p>
                <p class="text-xs">Try adjusting your date range or filters</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="p-4 border-t border-slate-200 dark:border-slate-700">
    {{ $trips->appends(request()->query())->links() }}
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart color scheme
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#e2e8f0' : '#374151';
    const gridColor = isDark ? '#374151' : '#e5e7eb';
    
    // Prepare chart data
    const chartData = @json($chartData ?? []);
    
    // Occupancy Trends Chart
    const occupancyCtx = document.getElementById('occupancyChart').getContext('2d');
    new Chart(occupancyCtx, {
        type: 'line',
        data: {
            labels: chartData.dates || [],
            datasets: [{
                label: 'Occupancy %',
                data: chartData.occupancy || [],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    ticks: { color: textColor },
                    grid: { color: gridColor },
                    min: 0,
                    max: 100
                }
            }
        }
    });

    // Route Performance Chart
    const routeCtx = document.getElementById('routeChart').getContext('2d');
    new Chart(routeCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.routes || [],
            datasets: [{
                data: chartData.routeRevenue || [],
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: chartData.dates || [],
            datasets: [{
                label: 'Daily Revenue ($)',
                data: chartData.revenue || [],
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: '#10b981',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    ticks: { 
                        color: textColor,
                        callback: function(value) {
                            return '$' + value.toFixed(0);
                        }
                    },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
</script>
@endsection
