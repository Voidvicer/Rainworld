@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">⛴️</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Ferry Services</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Island-hopping made easy with our regular ferry connections</p>
    </div>
  </div>
  <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur rounded-xl p-4 border border-slate-200 dark:border-slate-700">
    <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
      <span class="w-5 h-5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-xs">ℹ️</span>
      <span>Choose a <strong>date</strong>, then pick a <strong>departure time</strong>, set quantity and purchase. Blocked or full schedules are hidden.</span>
    </div>
  </div>
</div>

@if(!$hasValidBooking)
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 mb-6">
  <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 text-sm">
    <span class="w-5 h-5 rounded bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 grid place-content-center text-xs">⚠️</span>
    <span>You need a valid hotel booking covering the trip date to purchase ferry tickets. <a href="{{ route('hotels.index') }}" class="underline hover:text-amber-800 dark:hover:text-amber-300">Book a hotel first</a>.</span>
  </div>
</div>
@endif

<div id="errorMessage" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
  <div class="flex items-center gap-2 text-red-700 dark:text-red-400 text-sm">
    <span class="w-5 h-5 rounded bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 grid place-content-center text-xs">⚠️</span>
    <span>You need to select at least one trip to proceed with booking.</span>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 mb-6">
  <div class="flex items-end gap-4 flex-wrap justify-between">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
      <div>
        <label class="label text-slate-700 dark:text-slate-300">Select Date</label>
        <input type="date" name="date" value="{{ $selectedDate ?? '' }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200 w-48" min="{{ date('Y-m-d') }}" onchange="this.form.submit()">
      </div>
      <div class="pt-2">
        <a href="{{ route('ferry.trips.index') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Clear Filter</a>
      </div>
    </form>
    <div class="flex gap-3">
            <a href="{{ route('ferry.tickets.index') }}" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center gap-2">
        <span class="text-lg">🎫</span>
        <span>My Tickets</span>
        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      </a>
      <button id="bookNowBtn" onclick="processBooking()" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-lg text-sm font-medium shadow-md transition-all transform hover:scale-[1.02] active:scale-[0.98]">
        <span class="w-4 h-4 rounded bg-white/20 grid place-content-center text-xs">⛴️</span>
        Book Now
      </button>
    </div>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
  <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
    <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
      <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-sm">🚢</span>
      Ferry Schedule
    </h2>
  </div>
  <div class="overflow-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3 text-center font-semibold">Select</th>
          <th class="px-6 py-3 text-left font-semibold">Trip Type</th>
          <th class="px-6 py-3 text-left font-semibold">Time</th>
          <th class="px-6 py-3 text-left font-semibold">Route</th>
          <th class="px-6 py-3 text-center font-semibold">Seats Left</th>
          <th class="px-6 py-3 text-right font-semibold">Price</th>
          <th class="px-6 py-3 text-center font-semibold">Quantity</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white/60 dark:bg-slate-900/20">
        @if(!$selectedDate)
        <tr>
          <td colspan="7" class="px-6 py-16 text-center text-slate-500 dark:text-slate-400">
            <div class="flex flex-col items-center gap-4">
              <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-teal-100 dark:from-indigo-900/50 dark:to-teal-900/50 grid place-content-center text-3xl">📅</div>
              <div>
                <p class="text-lg font-medium text-slate-700 dark:text-slate-300">Select a Travel Date</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                  Choose your travel date above to see available ferry schedules for departures and returns
                </p>
              </div>
            </div>
          </td>
        </tr>
        @else
          @forelse($trips as $trip)
          <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors group">
            <td class="px-6 py-4 text-center">
              <input type="checkbox" class="trip-checkbox {{ $trip->trip_type }}-trip w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500" 
                     data-type="{{ $trip->trip_type }}" 
                     data-time="{{ $trip->depart_time }}" 
                     data-price="{{ $trip->price }}"
                     data-trip-id="{{ $trip->id }}"
                     data-origin="{{ $trip->origin }}"
                     data-destination="{{ $trip->destination }}">
            </td>
            <td class="px-6 py-4">
              @if($trip->trip_type === 'departure')
              <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded text-xs font-semibold">Departure</span>
              @else
              <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded text-xs font-semibold">Return</span>
              @endif
            </td>
            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
              <span class="font-mono bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ date('H:i', strtotime($trip->depart_time)) }}</span>
            </td>
            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
              <div class="flex items-center gap-2">
                <span class="font-medium">{{ $trip->origin }}</span>
                <span class="text-slate-400 dark:text-slate-500">→</span>
                <span class="font-medium">{{ $trip->destination }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              @php
              $remaining = $trip->remainingSeats();
              @endphp
              <span class="font-semibold {{ $remaining > 10 ? 'text-emerald-600 dark:text-emerald-400' : ($remaining > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                {{ $remaining }}
              </span>
            </td>
            <td class="px-6 py-4 text-right font-bold text-lg text-slate-800 dark:text-slate-200">${{ number_format($trip->price, 2) }}</td>
            <td class="px-6 py-4 text-center">
              <input type="number" class="quantity-input w-20 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm" 
                     value="1" min="1" max="{{ $trip->remainingSeats() }}" disabled>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
                <div>
                  <p class="font-medium">No ferry trips available</p>
                  <p class="text-sm text-slate-400 dark:text-slate-500">
                    No trips scheduled for {{ date('F j, Y', strtotime($selectedDate)) }}
                  </p>
                </div>
              </div>
            </td>
          </tr>
          @endforelse
        @endif
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('.trip-checkbox');
  const quantityInputs = document.querySelectorAll('.quantity-input');
  
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const row = this.closest('tr');
      const quantityInput = row.querySelector('.quantity-input');
      
      if (this.checked) {
        quantityInput.disabled = false;
        quantityInput.focus();
      } else {
        quantityInput.disabled = true;
        quantityInput.value = 1;
      }
    });
  });
});

function processBooking() {
  const selectedDate = document.querySelector('input[name="date"]').value;
  if (!selectedDate) {
    showError('Please select a travel date first.');
    return;
  }

  // Check if user has valid hotel booking
  const hasValidBooking = {{ $hasValidBooking ? 'true' : 'false' }};
  if (!hasValidBooking) {
    showError('You need a valid hotel booking covering the trip date to purchase ferry tickets.');
    return;
  }

  const departureTrips = document.querySelectorAll('.departure-trip:checked');
  const returnTrips = document.querySelectorAll('.return-trip:checked');
  
  if (departureTrips.length === 0 && returnTrips.length === 0) {
    showError('You need to select at least one trip to proceed with booking.');
    return;
  }
  
  // Hide error message
  document.getElementById('errorMessage').classList.add('hidden');
  
  // Collect booking data
  const bookingData = {
    date: selectedDate,
    trips: []
  };
  
  // Add departure trips
  departureTrips.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const quantity = parseInt(row.querySelector('.quantity-input').value);
    bookingData.trips.push({
      trip_id: parseInt(checkbox.dataset.tripId),
      type: 'departure',
      time: checkbox.dataset.time,
      price: parseFloat(checkbox.dataset.price),
      quantity: quantity,
      origin: checkbox.dataset.origin,
      destination: checkbox.dataset.destination
    });
  });
  
  // Add return trips
  returnTrips.forEach(checkbox => {
    const row = checkbox.closest('tr');
    const quantity = parseInt(row.querySelector('.quantity-input').value);
    bookingData.trips.push({
      trip_id: parseInt(checkbox.dataset.tripId),
      type: 'return',
      time: checkbox.dataset.time,
      price: parseFloat(checkbox.dataset.price),
      quantity: quantity,
      origin: checkbox.dataset.origin,
      destination: checkbox.dataset.destination
    });
  });
  
  // Create a form and submit to a new bulk booking endpoint
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("ferry.tickets.bulk.prepare") }}';
  
  // Add CSRF token
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = '{{ csrf_token() }}';
  form.appendChild(csrfInput);
  
  // Add booking data
  const dataInput = document.createElement('input');
  dataInput.type = 'hidden';
  dataInput.name = 'booking_data';
  dataInput.value = JSON.stringify(bookingData);
  form.appendChild(dataInput);
  
  document.body.appendChild(form);
  form.submit();
}

function showError(message) {
  const errorDiv = document.getElementById('errorMessage');
  errorDiv.querySelector('span:last-child').textContent = message;
  errorDiv.classList.remove('hidden');
  errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
@endsection
