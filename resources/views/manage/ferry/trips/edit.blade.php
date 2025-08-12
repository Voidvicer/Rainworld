@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">⛴️</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Edit Ferry Trip</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Update ferry trip details</p>
    </div>
  </div>
</div>

<div class="max-w-2xl mx-auto">
  <form method="POST" action="{{ route('manage.ferry-trips.update',$trip) }}" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">@csrf @method('PUT')
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
      <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
        <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-sm">✏️</span>
        Trip Details
      </h2>
    </div>
    <div class="p-6 space-y-5">
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label text-slate-700 dark:text-slate-300">Date</label><input type="date" name="date" value="{{ $trip->date }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Trip Type</label>
          <select name="trip_type" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required onchange="updateOriginDestination()">
            <option value="">Select Type</option>
            <option value="departure" {{ $trip->trip_type === 'departure' ? 'selected' : '' }}>Departure (Male' City → Picnic Island)</option>
            <option value="return" {{ $trip->trip_type === 'return' ? 'selected' : '' }}>Return (Picnic Island → Male' City)</option>
          </select>
        </div>
        <div><label class="label text-slate-700 dark:text-slate-300">Depart Time</label><input type="time" name="depart_time" value="{{ $trip->depart_time }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Origin</label><input type="text" name="origin" value="{{ $trip->origin }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Destination</label><input type="text" name="destination" value="{{ $trip->destination }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Capacity</label><input type="number" name="capacity" value="{{ $trip->capacity }}" min="1" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Price</label><input type="number" step="0.01" name="price" value="{{ $trip->price }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
      </div>
      <label class="inline-flex items-center gap-2"><input type="checkbox" name="blocked" value="1" class="rounded border-slate-300" {{ $trip->blocked ? 'checked' : '' }}> <span class="text-sm text-slate-700 dark:text-slate-300">Blocked (hide from visitors)</span></label>
      <div class="flex gap-3 pt-2">
        <button class="btn-primary">Update Trip</button>
        <a href="{{ route('manage.ferry-trips.index') }}" class="px-6 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">Cancel</a>
      </div>
    </div>
  </form>
</div>

<script>
function updateOriginDestination() {
  const tripType = document.querySelector('select[name="trip_type"]').value;
  const originInput = document.querySelector('input[name="origin"]');
  const destinationInput = document.querySelector('input[name="destination"]');
  
  if (tripType === 'departure') {
    originInput.value = "Male' City";
    destinationInput.value = "Picnic Island";
  } else if (tripType === 'return') {
    originInput.value = "Picnic Island";
    destinationInput.value = "Male' City";
  }
}
</script>
@endsection
