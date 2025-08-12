@extends('layouts.app')
@section('content')
<div class="space-y-12">
  <!-- Map + Promotions Row -->
  <div class="grid gap-8 xl:grid-cols-3 items-start">
    <div class="xl:col-span-2 relative group rounded-3xl ring-1 ring-slate-300 dark:ring-slate-700 overflow-hidden shadow-lg bg-gradient-to-br from-white/90 via-white/70 to-white/30 dark:from-slate-800/90 dark:via-slate-800/70 dark:to-slate-800/40 backdrop-blur transition">
      <div id="map" class="h-[480px] w-full"></div>
      <div class="absolute top-4 left-4 px-4 py-2 rounded-full bg-white/80 dark:bg-slate-900/70 backdrop-blur text-xs font-semibold text-slate-700 dark:text-slate-200 tracking-wide shadow">Island Map</div>
      <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-40 transition bg-[radial-gradient(circle_at_30%_30%,rgba(99,102,241,.25),transparent_60%),radial-gradient(circle_at_70%_70%,rgba(14,165,233,.25),transparent_60%)]"></div>
    </div>
    <aside class="space-y-5">
      <h2 class="text-xl font-semibold flex items-center gap-2"><span class="w-1.5 h-6 bg-gradient-to-b from-indigo-500 to-teal-500 rounded"></span>Promotions</h2>
      <div class="space-y-4">
        @forelse($promos as $p)
          <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 backdrop-blur shadow-sm hover:shadow-md transition overflow-hidden relative group ring-1 ring-transparent hover:ring-indigo-400/40">
            <div class="absolute -inset-px opacity-0 group-hover:opacity-100 transition pointer-events-none bg-gradient-to-r from-indigo-500/10 via-fuchsia-500/10 to-teal-500/10"></div>
            @if($p->image_url)<img src="{{ $p->image_url }}" alt class="w-full h-32 object-cover">@endif
            <div class="p-4 relative">
              <h3 class="font-semibold text-sm mb-1 dark:text-slate-100 flex items-start gap-2">
                <span class="text-indigo-500 dark:text-indigo-400 mt-0.5">★</span>{{ $p->title }}
              </h3>
              <p class="text-xs text-slate-600 dark:text-slate-400 leading-snug">{{ $p->content }}</p>
              <div class="mt-2 text-[10px] uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $p->scope }} promo</div>
            </div>
          </div>
        @empty
          <p class="text-sm text-slate-500">No current promotions.</p>
        @endforelse
      </div>
    </aside>
  </div>

  <!-- Featured Hotels -->
  <section class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <h2 class="text-2xl font-semibold flex items-center gap-3"><span class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-sm shadow">🏨</span>Featured Hotels</h2>
      <a href="{{ route('hotels.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Browse all →</a>
    </div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($hotels as $h)
        <a href="{{ route('hotels.show',$h) }}" class="group relative rounded-2xl p-0.5 bg-gradient-to-br from-indigo-500/60 via-fuchsia-500/40 to-teal-500/60 hover:from-indigo-500 hover:via-fuchsia-500 hover:to-teal-500 transition shadow-md">
          <div class="h-full w-full rounded-[1rem] bg-white/90 dark:bg-slate-900/80 backdrop-blur p-4 flex flex-col justify-between ring-1 ring-slate-200/60 dark:ring-slate-700/60">
            <div>
              <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-1 tracking-tight">{{ $h->name }}</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ $h->address }}</p>
            </div>
            <div class="mt-4 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
              <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 font-medium transition">View Details</span>
              <span class="opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition">→</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </section>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    try {
      const map = L.map('map',{scrollWheelZoom:false, attributionControl:true}).setView([4.1753, 73.5093], 15);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap'}).addTo(map);
      const locations = @json($locations);
      const icon = L.icon({iconUrl:'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png', iconSize:[25,41], iconAnchor:[12,41], popupAnchor:[1,-34]});
      locations.forEach(loc=>{ if(loc.lat&&loc.lng){ L.marker([loc.lat,loc.lng],{icon}).addTo(map).bindPopup(`<strong>${loc.name}</strong><br>${loc.description||''}`); } });
      setTimeout(()=> map.invalidateSize(), 300); // ensure proper sizing when in responsive container
    } catch(e) { console.warn('Map init failed', e); }
  });
</script>
@endsection
