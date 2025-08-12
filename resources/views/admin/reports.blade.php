@extends('layouts.app')
@section('content')
<div class="mb-8">
  <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100 mb-6">System Analytics & Reports</h1>
</div>

<!-- Revenue Overview Cards -->
<div class="grid gap-6 md:grid-cols-3 mb-8">
  <div class="rounded-xl p-5 bg-white/70 backdrop-blur ring-1 ring-slate-200 shadow relative overflow-hidden">
    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Hotel Revenue</div>
    <div class="text-2xl font-bold text-indigo-700">${{ number_format($hotelRevenue,2) }}</div>
    <div class="absolute -right-4 -bottom-4 opacity-10 text-[120px] font-black select-none">H</div>
  </div>
  <div class="rounded-xl p-5 bg-white/70 backdrop-blur ring-1 ring-slate-200 shadow relative overflow-hidden">
    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Ferry Revenue</div>
    <div class="text-2xl font-bold text-indigo-700">${{ number_format($ferryRevenue,2) }}</div>
    <div class="absolute -right-4 -bottom-4 opacity-10 text-[120px] font-black select-none">F</div>
  </div>
  <div class="rounded-xl p-5 bg-white/70 backdrop-blur ring-1 ring-slate-200 shadow relative overflow-hidden">
    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Park Revenue</div>
    <div class="text-2xl font-bold text-indigo-700">${{ number_format($parkRevenue,2) }}</div>
    <div class="absolute -right-4 -bottom-4 opacity-10 text-[120px] font-black select-none">P</div>
  </div>
</div>

<!-- Enhanced Analytics Grid -->
<div class="grid gap-6 lg:grid-cols-2 mb-8">
  <!-- Monthly Revenue Breakdown -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Monthly Performance</h2>
    </div>
    <div class="p-6">
      <div class="space-y-4">
        @foreach($monthlyStats as $month)
          <div class="flex items-center justify-between">
            <div class="font-medium text-slate-900 dark:text-slate-100">{{ $month['month'] }}</div>
            <div class="flex items-center gap-4">
              <div class="text-right">
                <div class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                  ${{ number_format($month['hotel_revenue'] + $month['ferry_revenue'], 2) }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $month['bookings'] }} bookings</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Hotel Occupancy Rates -->
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Hotel Occupancy (Last 7 Days)</h2>
    </div>
    <div class="p-6">
      <div class="space-y-3">
        @foreach($occupancyRates as $day)
          <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600 dark:text-slate-400">{{ $day['date'] }}</span>
            <div class="flex items-center gap-3 flex-1 max-w-32 ml-4">
              <div class="flex-1 bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300
                  {{ $day['rate'] > 80 ? 'bg-red-500' : ($day['rate'] > 60 ? 'bg-amber-500' : 'bg-green-500') }}"
                  style="width: {{ $day['rate'] }}%"></div>
              </div>
              <span class="text-sm font-medium text-slate-900 dark:text-slate-100 w-12 text-right">{{ $day['rate'] }}%</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<!-- User Growth Chart -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">User Growth (Last 12 Months)</h2>
  </div>
  <div class="p-6">
    <canvas id="userGrowthChart" height="100"></canvas>
  </div>
</div>

<!-- Charts Grid -->
<div class="mt-10 grid gap-10 lg:grid-cols-2">
  <div class="rounded-2xl bg-white/70 backdrop-blur ring-1 ring-slate-200 shadow p-6">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600 mb-4">Daily Revenue (Last 14 Days)</h2>
    <canvas id="revenueLines" height="180"></canvas>
  </div>
  <div class="rounded-2xl bg-white/70 backdrop-blur ring-1 ring-slate-200 shadow p-6">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-600 mb-4">Revenue Distribution</h2>
    <canvas id="revenuePie" height="180"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const labels = @json($chartLabels);
  const hotelData = @json($hotelSeries);
  const ferryData = @json($ferrySeries);
  const parkData = @json($parkSeries);
  const userGrowthData = @json($userGrowth);

  // Daily Revenue Chart
  const ctxLine = document.getElementById('revenueLines').getContext('2d');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {label:'Hotel', data:hotelData, borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,.15)', tension:.35, fill:true},
        {label:'Ferry', data:ferryData, borderColor:'#0ea5e9', backgroundColor:'rgba(14,165,233,.15)', tension:.35, fill:true},
        {label:'Park', data:parkData, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.15)', tension:.35, fill:true},
      ]
    },
    options:{
      responsive:true,
      interaction:{mode:'index',intersect:false},
      stacked:false,
      plugins:{legend:{display:true, labels:{boxWidth:12,font:{size:11}}}},
      scales:{
        y:{beginAtZero:true, ticks:{callback:v=>'$'+v}},
        x:{ticks:{maxRotation:0}}
      }
    }
  });

  // Revenue Distribution Pie Chart
  const ctxPie = document.getElementById('revenuePie').getContext('2d');
  new Chart(ctxPie, {
    type: 'doughnut',
    data: {
      labels:['Hotel','Ferry','Park'],
      datasets:[{
        data:[hotelData.reduce((a,b)=>a+b,0), ferryData.reduce((a,b)=>a+b,0), parkData.reduce((a,b)=>a+b,0)],
        backgroundColor:['#6366f1','#0ea5e9','#10b981'],
        borderWidth:0,
        hoverOffset:6
      }]
    },
    options:{plugins:{legend:{position:'bottom', labels:{boxWidth:12,font:{size:11}}}}}
  });

  // User Growth Chart
  const ctxUserGrowth = document.getElementById('userGrowthChart').getContext('2d');
  new Chart(ctxUserGrowth, {
    type: 'bar',
    data: {
      labels: userGrowthData.map(d => d.month),
      datasets: [{
        label: 'New Users',
        data: userGrowthData.map(d => d.new_users),
        backgroundColor: 'rgba(99, 102, 241, 0.8)',
        borderColor: '#6366f1',
        borderWidth: 1,
        borderRadius: 4,
        borderSkipped: false,
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
          ticks: { stepSize: 1 }
        },
        x: { 
          ticks: { maxRotation: 0 }
        }
      }
    }
  });
</script>
@endsection
