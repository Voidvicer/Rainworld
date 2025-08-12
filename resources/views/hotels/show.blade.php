@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-start gap-4">
    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-xl shadow">🏨</div>
    <div class="flex-1">
      <h1 class="text-3xl font-bold tracking-tight mb-2 text-slate-800 dark:text-slate-100">{{ $hotel->name }}</h1>
      <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $hotel->description }}</p>
      <div class="mt-3 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-500">
        <span class="w-4 h-4 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-xs">📍</span>
        <span>{{ $hotel->address }}</span>
      </div>
    </div>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden mb-8">
  <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
    <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
      <span class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-sm">🛏️</span>
      Available Rooms
    </h2>
  </div>
  
  <div class="p-6">
    <div class="grid gap-6 lg:grid-cols-2">
      @foreach($hotel->rooms as $room)
        <div class="group relative rounded-2xl bg-white/90 dark:bg-slate-900/60 backdrop-blur border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition overflow-hidden">
          <div class="p-5">
            <div class="flex items-start justify-between mb-3">
              <div>
                <h3 class="font-semibold text-lg mb-1 text-slate-800 dark:text-slate-100 flex items-center gap-2">
                  {{ $room->name }}
                  <span class="px-2 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded text-xs font-semibold capitalize">{{ $room->type }}</span>
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Sleeps up to <span class="font-medium">{{ $room->capacity }}</span> guests</p>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($room->price_per_night,2) }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-500">per night</div>
              </div>
            </div>
            
            <form method="POST" action="{{ route('bookings.prepare',$room) }}" class="mt-4 space-y-4" oninput="calc_{{ $room->id }}()">@csrf
              <div class="grid gap-3">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="label text-slate-700 dark:text-slate-300">Check In</label>
                    <input type="date" name="check_in" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required>
                  </div>
                  <div>
                    <label class="label text-slate-700 dark:text-slate-300">Check Out</label>
                    <input type="date" name="check_out" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required>
                  </div>
                </div>
                <div>
                  <label class="label text-slate-700 dark:text-slate-300">Guests</label>
                  <input type="number" name="guests" min="1" max="{{ $room->capacity }}" value="1" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200 w-full" required>
                </div>
              </div>
              
              <div id="pricePreview{{ $room->id }}" class="text-sm text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-lg px-3 py-2 border border-slate-200 dark:border-slate-700">
                Select dates to see total
              </div>
              
              <button class="btn-primary w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-md transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                Proceed to Payment
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<script>
  function nightsDiff(ci,co){if(!ci||!co) return 0; const a=new Date(ci);const b=new Date(co); return (b-a)/(1000*60*60*24);} 
  @foreach($hotel->rooms as $room)
  function calc_{{ $room->id }}(){
    const wrap=document.querySelector('form[action="{{ route('bookings.prepare',$room) }}"]');
    const ci=wrap.querySelector('[name=check_in]').value; const co=wrap.querySelector('[name=check_out]').value; const n=nightsDiff(ci,co); const price={{ $room->price_per_night }}; const el=document.getElementById('pricePreview{{ $room->id }}');
    if(n>0){el.innerHTML='Total for '+n+' night'+(n>1?'s':'')+': <span class="font-semibold text-slate-800">$'+(n*price).toFixed(2)+'</span>';}
    else el.textContent='Select dates to see total.';
  }
  @endforeach
</script>
@endsection
