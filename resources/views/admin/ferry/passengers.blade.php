@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/50 grid place-content-center text-xl">⛴️</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Ferry Passenger Lists</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">View passenger manifests and issue ferry passes</p>
          </div>
        </div>
        <a href="{{ route('admin.ferry.dashboard') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Ferry Dashboard
        </a>
      </div>
    </div>
    
    <div class="p-6">
      <!-- Date Selector -->
      <form method="GET" class="mb-6">
        <div class="flex items-center gap-4">
          <div>
            <label for="date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Travel Date</label>
            <input type="date" id="date" name="date" value="{{ $selectedDate }}" 
                   class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="mt-6">
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
              Load Passenger Lists
            </button>
          </div>
        </div>
      </form>
      
      <!-- Ferry Pass Issuer -->
      <div class="bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Issue Ferry Pass</h3>
        <form method="POST" action="{{ route('admin.ferry.issue-pass') }}" class="flex gap-4">
          @csrf
          <div class="flex-1">
            <input type="text" name="ticket_code" placeholder="Enter ticket code..." required
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-cyan-500 focus:ring-cyan-500">
          </div>
          <div class="flex items-center gap-2">
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
              <input type="checkbox" name="booking_verification" value="1" checked
                     class="rounded border-slate-300 dark:border-slate-600 text-cyan-600 focus:ring-cyan-500">
              Verify hotel booking
            </label>
          </div>
          <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
            Issue Pass
          </button>
        </form>
        @if(session('passData'))
          <div class="mt-4 p-4 bg-green-100 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center gap-2 text-green-700 dark:text-green-400 font-medium mb-2">
              ✅ Ferry Pass Issued Successfully
            </div>
            @php($pass = session('passData'))
            <div class="text-sm space-y-1">
              <p><strong>Passenger:</strong> {{ $pass['passenger_name'] }}</p>
              <p><strong>Trip:</strong> {{ $pass['trip_details']['origin'] }} → {{ $pass['trip_details']['destination'] }} at {{ date('g:i A', strtotime($pass['trip_details']['depart_time'])) }}</p>
              <p><strong>Quantity:</strong> {{ $pass['quantity'] }} passenger(s)</p>
              <p><strong>Hotel Booking:</strong> {{ $pass['valid_booking'] ? '✅ Valid' : '❌ Not Found' }}</p>
            </div>
          </div>
        @endif
      </div>
      
      <!-- Trip Passenger Lists -->
      <div class="space-y-6">
        @forelse($trips as $trip)
          @php
            $totalPassengers = $trip->tickets->sum('quantity');
            $occupancyRate = round(($totalPassengers / $trip->capacity) * 100, 1);
          @endphp
          <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-600">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                    {{ $trip->origin }} → {{ $trip->destination }}
                  </h3>
                  <p class="text-sm text-slate-600 dark:text-slate-400">
                    Departure: {{ date('g:i A', strtotime($trip->depart_time)) }} • 
                    {{ ucfirst($trip->trip_type) }} Trip
                  </p>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalPassengers }}/{{ $trip->capacity }}</div>
                  <div class="text-sm {{ $occupancyRate > 90 ? 'text-red-600 dark:text-red-400' : ($occupancyRate > 70 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400') }}">
                    {{ $occupancyRate }}% capacity
                  </div>
                </div>
              </div>
              
              <!-- Capacity Bar -->
              <div class="mt-4">
                <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                  <div class="h-2 rounded-full transition-all duration-300
                    {{ $occupancyRate > 90 ? 'bg-red-500' : ($occupancyRate > 70 ? 'bg-amber-500' : 'bg-green-500') }}"
                    style="width: {{ min($occupancyRate, 100) }}%"></div>
                </div>
              </div>
            </div>
            
            @if($trip->tickets->isNotEmpty())
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                  <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passenger</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ticket Code</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Quantity</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel Booking</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($trip->tickets as $ticket)
                      @php
                        $hasValidBooking = \App\Models\Booking::where('user_id', $ticket->user_id)
                          ->where('status', '!=', 'canceled')
                          ->whereDate('check_in', '<=', $trip->date)
                          ->whereDate('check_out', '>=', $trip->date)
                          ->exists();
                      @endphp
                      <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 grid place-content-center text-white font-semibold text-xs">
                              {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $ticket->user->name }}</div>
                              <div class="text-sm text-slate-500 dark:text-slate-400">{{ $ticket->user->email }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span class="font-mono text-xs font-medium text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">
                            {{ $ticket->code }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-900 dark:text-slate-100">
                          {{ $ticket->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-emerald-600 dark:text-emerald-400">
                          ${{ number_format($ticket->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($ticket->status === 'paid') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400
                            @elseif($ticket->status === 'completed') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                            @else bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400 @endif">
                            {{ ucfirst($ticket->status) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $hasValidBooking ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                            {{ $hasValidBooking ? '✅ Valid' : '❌ None' }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="p-8 text-center text-slate-500 dark:text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl mx-auto mb-3">🎫</div>
                <p>No passengers booked for this trip</p>
              </div>
            @endif
          </div>
        @empty
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl mx-auto mb-4">⛴️</div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No ferry trips scheduled for {{ date('F j, Y', strtotime($selectedDate)) }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Try selecting a different date</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
