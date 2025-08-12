@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">⛴️</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Create Ferry Trip</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Add a new ferry trip to the schedule</p>
    </div>
  </div>
</div>

<div class="max-w-2xl mx-auto">
  <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
      <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
        <span class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-sm">⚡</span>
        Quick Add Default Times
      </h2>
      <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Click to quickly add all default departure or return times for a date</p>
    </div>
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="label text-slate-700 dark:text-slate-300">Select Date</label>
          <input type="date" id="quickDate" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" min="{{ date('Y-m-d') }}">
        </div>
        <div>
          <button onclick="addDefaultTimes('departure')" class="w-full px-4 py-3 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-lg text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-900/70 transition">
            Add Default Departures
          </button>
        </div>
        <div>
          <button onclick="addDefaultTimes('return')" class="w-full px-4 py-3 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/70 transition">
            Add Default Returns
          </button>
        </div>
        <div class="flex justify-center">
          <button type="button" onclick="toggleDefaultTimesInfo()" class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-900/70 transition flex items-center justify-center text-sm font-semibold">
            ℹ️
          </button>
        </div>
      </div>
      
      <!-- Info Panel -->
      <div id="defaultTimesInfo" class="hidden mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600">
        <h4 class="font-semibold text-slate-800 dark:text-slate-200 mb-2">Default Ferry Times</h4>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
          <div>
            <h5 class="font-medium text-emerald-600 dark:text-emerald-400 mb-1">Departure Times (Male' City → Picnic Island)</h5>
            <ul class="text-slate-600 dark:text-slate-400 space-y-1">
              <li>• 07:00 - Early Morning</li>
              <li>• 08:00 - Morning</li>
              <li>• 09:00 - Mid-Morning</li>
              <li>• 10:00 - Late Morning</li>
            </ul>
          </div>
          <div>
            <h5 class="font-medium text-blue-600 dark:text-blue-400 mb-1">Return Times (Picnic Island → Male' City)</h5>
            <ul class="text-slate-600 dark:text-slate-400 space-y-1">
              <li>• 14:00 - Early Afternoon</li>
              <li>• 16:00 - Afternoon</li>
              <li>• 18:00 - Evening</li>
              <li>• 20:00 - Night</li>
            </ul>
          </div>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-500 mt-3">
          <strong>Note:</strong> Each trip automatically sets capacity to 50 passengers and price to $15.00
        </p>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('manage.ferry-trips.store') }}" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">@csrf
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
      <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
        <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-sm">+</span>
        Trip Details
      </h2>
    </div>
    <div class="p-6 space-y-5">
      <div class="grid grid-cols-2 gap-4">
        <div><label class="label text-slate-700 dark:text-slate-300">Date</label><input type="date" name="date" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required min="{{ date('Y-m-d') }}"></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Trip Type</label>
          <select name="trip_type" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required onchange="updateOriginDestination()">
            <option value="">Select Type</option>
            <option value="departure">Departure (Male' City → Picnic Island)</option>
            <option value="return">Return (Picnic Island → Male' City)</option>
          </select>
        </div>
        <div><label class="label text-slate-700 dark:text-slate-300">Depart Time</label><input type="time" name="depart_time" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Origin</label><input type="text" name="origin" value="Male' City" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Destination</label><input type="text" name="destination" value="Picnic Island" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Capacity</label><input type="number" name="capacity" value="50" min="1" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
        <div><label class="label text-slate-700 dark:text-slate-300">Price</label><input type="number" step="0.01" name="price" value="15" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required></div>
      </div>
      <label class="inline-flex items-center gap-2"><input type="checkbox" name="blocked" value="1" class="rounded border-slate-300"> <span class="text-sm text-slate-700 dark:text-slate-300">Blocked (hide from visitors)</span></label>
      <div class="flex gap-3 pt-2">
        <button class="btn-primary">Create Trip</button>
        <a href="{{ route('manage.ferry-trips.index') }}" class="px-6 py-2 border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">Cancel</a>
      </div>
    </div>
  </form>
</div>

<script>
function toggleDefaultTimesInfo() {
  const infoPanel = document.getElementById('defaultTimesInfo');
  infoPanel.classList.toggle('hidden');
}

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

async function addDefaultTimes(type) {
  const date = document.getElementById('quickDate').value;
  if (!date) {
    alert('Please select a date first');
    return;
  }
  
  const times = type === 'departure' ? ['07:00', '08:00', '09:00', '10:00'] : ['14:00', '16:00', '18:00', '20:00'];
  const origin = type === 'departure' ? "Male' City" : "Picnic Island";
  const destination = type === 'departure' ? "Picnic Island" : "Male' City";
  
  let created = 0;
  for (const time of times) {
    try {
      const formData = new FormData();
      formData.append('_token', '{{ csrf_token() }}');
      formData.append('date', date);
      formData.append('trip_type', type);
      formData.append('depart_time', time);
      formData.append('origin', origin);
      formData.append('destination', destination);
      formData.append('capacity', '50');
      formData.append('price', '15.00');
      
      const response = await fetch('{{ route("manage.ferry-trips.store") }}', {
        method: 'POST',
        body: formData
      });
      
      if (response.ok) {
        created++;
      }
    } catch (error) {
      console.error('Error creating trip:', error);
    }
  }
  
  if (created > 0) {
    alert(`Created ${created} ${type} trips for ${date}`);
    // Optionally redirect to index page
    window.location.href = '{{ route("manage.ferry-trips.index") }}';
  } else {
    alert('Failed to create trips. They may already exist.');
  }
}
</script>
@endsection
