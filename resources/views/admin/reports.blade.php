@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">System Reports</h1>
<div class="grid gap-6 md:grid-cols-3">
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
</script>
@endsection
