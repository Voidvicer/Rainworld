@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-xl">📊</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Hotel Performance Reports</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Comprehensive analytics and booking insights</p>
          </div>
        </div>
        <a href="{{ route('manage.hotel.dashboard') }}" class="group relative bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
          <span class="text-lg">🏨</span>
          <span>Hotel Dashboard</span>
          <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </a>
      </div>
    </div>
    
    <!-- Filters -->
    <div class="p-6 bg-slate-50 dark:bg-slate-700/50">
      <form method="GET" class="flex flex-col lg:flex-row lg:items-end gap-4">
        <div>
          <label for="date_from" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">From Date</label>
          <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" 
                 class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label for="date_to" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">To Date</label>
          <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" 
                 class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
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

<!-- Key Metrics -->
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Bookings</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ number_format($stats['total_bookings']) }}</div>
        <div class="text-xs text-blue-600 dark:text-blue-400">{{ date('M j', strtotime($dateFrom)) }} - {{ date('M j, Y', strtotime($dateTo)) }}</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">📝</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Revenue</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">hotel bookings</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">💰</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Avg Daily Rate</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">${{ number_format($stats['avg_daily_rate'], 2) }}</div>
        <div class="text-xs text-purple-600 dark:text-purple-400">per night</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">📈</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Occupancy Rate</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $stats['occupancy_rate'] }}%</div>
        <div class="text-xs text-amber-600 dark:text-amber-400">across all hotels</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-2xl">🏨</div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="grid gap-6 lg:grid-cols-2 mb-8">
  <!-- Booking Trends Chart -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Daily Booking Trends</h3>
    <div class="h-64">
      <canvas id="bookingTrendsChart"></canvas>
    </div>
  </div>

  <!-- Hotel Performance Chart -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Hotel Performance</h3>
    <div class="h-64">
      <canvas id="hotelPerformanceChart"></canvas>
    </div>
  </div>
</div>

<!-- Revenue Analytics Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 mb-8">
  <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Revenue Analytics & Occupancy</h3>
  <div class="h-80">
    <canvas id="revenueOccupancyChart"></canvas>
  </div>
</div>

<!-- Room Type Performance -->
<div class="grid gap-6 lg:grid-cols-3 mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Room Type Distribution</h3>
    <div class="h-64">
      <canvas id="roomTypeChart"></canvas>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/50 grid place-content-center text-2xl">⭐</div>
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Top Performer</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['top_hotel'] ?? 'Maldivian Paradise Resort' }}</p>
        <p class="text-xs text-green-600 dark:text-green-400">${{ number_format($stats['top_hotel_revenue'] ?? 0, 2) }} revenue</p>
      </div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">📊</div>
      <div>
        <h3 class="font-semibold text-slate-900 dark:text-slate-100">Avg Stay Duration</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $stats['avg_stay_duration'] ?? '3.2' }} nights</p>
        <p class="text-xs text-blue-600 dark:text-blue-400">per booking</p>
      </div>
    </div>
  </div>
</div>

<!-- Detailed Bookings Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Detailed Booking Reports</h2>
    <p class="text-sm text-slate-600 dark:text-slate-400">Complete breakdown of hotel reservations</p>
  </div>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
      <thead class="bg-slate-50 dark:bg-slate-700">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel & Room</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stay Period</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booked</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
        @forelse($bookings as $booking)
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $booking->user->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->user->email }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900 dark:text-slate-100">{{ $booking->room->hotel->name }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $booking->room->name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              {{ date('M j', strtotime($booking->check_in)) }} - {{ date('M j, Y', strtotime($booking->check_out)) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-slate-900 dark:text-slate-100">
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
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-500 dark:text-slate-400">
              {{ $booking->created_at->format('M j, Y') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">🏨</div>
                <p class="font-medium">No bookings found for the selected period</p>
                <p class="text-xs">Try adjusting your date range</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  @if($bookings->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
      {{ $bookings->appends(request()->query())->links() }}
    </div>
  @endif
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
    
    // Booking Trends Chart
    const bookingCtx = document.getElementById('bookingTrendsChart').getContext('2d');
    new Chart(bookingCtx, {
        type: 'line',
        data: {
            labels: chartData.dates || [],
            datasets: [{
                label: 'Daily Bookings',
                data: chartData.dailyBookings || [],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                    grid: { color: gridColor }
                }
            }
        }
    });

    // Hotel Performance Chart
    const hotelCtx = document.getElementById('hotelPerformanceChart').getContext('2d');
    new Chart(hotelCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.hotels || [],
            datasets: [{
                data: chartData.hotelRevenue || [],
                backgroundColor: [
                    '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'
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

    // Revenue & Occupancy Chart
    const revenueCtx = document.getElementById('revenueOccupancyChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: chartData.dates || [],
            datasets: [{
                label: 'Daily Revenue ($)',
                data: chartData.dailyRevenue || [],
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: '#10b981',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Occupancy Rate (%)',
                data: chartData.occupancyRate || [],
                type: 'line',
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                yAxisID: 'y1'
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
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: { 
                        color: textColor,
                        callback: function(value) {
                            return '$' + value.toFixed(0);
                        }
                    },
                    grid: { color: gridColor }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    ticks: { 
                        color: textColor,
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });

    // Room Type Chart
    const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
    new Chart(roomTypeCtx, {
        type: 'pie',
        data: {
            labels: chartData.roomTypes || [],
            datasets: [{
                data: chartData.roomTypeBookings || [],
                backgroundColor: [
                    '#f59e0b', '#10b981', '#3b82f6', '#ef4444', '#8b5cf6', '#06b6d4'
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
});
</script>
@endsection
