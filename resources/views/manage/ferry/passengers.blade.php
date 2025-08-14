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
            <p class="text-sm text-slate-600 dark:text-slate-400">View passenger manifests and manage ferry operations</p>
          </div>
        </div>
        <a href="{{ route('manage.ferry.dashboard') }}" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
          <span class="text-lg">📊</span>
          <span>Ferry Dashboard</span>
          <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
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
            <button type="submit" class="group relative bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
              <span class="text-lg">📋</span>
              <span>Load Passenger Lists</span>
              <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </button>
          </div>
        </div>
      </form>
      
      <!-- Trip Passenger Lists -->
      <div class="space-y-6">
        @forelse($trips as $trip)
          @php
            $bookedTickets = $trip->tickets->where('status', 'paid');
            $totalPassengers = $bookedTickets->sum('quantity');
            $utilizationPercentage = $trip->capacity > 0 ? round(($totalPassengers / $trip->capacity) * 100, 1) : 0;
          @endphp
          
          <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-600">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                    {{ $trip->origin }} → {{ $trip->destination }}
                  </h3>
                  <div class="flex items-center gap-4 mt-1">
                    <span class="text-sm text-slate-600 dark:text-slate-400">
                      {{ date('g:i A', strtotime($trip->depart_time)) }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full capitalize
                      @if($trip->status === 'scheduled') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                      @elseif($trip->status === 'boarding') bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400
                      @elseif($trip->status === 'departed') bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400
                      @else bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400 @endif">
                      {{ $trip->status ?? 'scheduled' }}
                    </span>
                    <span class="text-xs px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-400 capitalize">
                      {{ $trip->trip_type }}
                    </span>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalPassengers }}</div>
                  <div class="text-sm text-slate-600 dark:text-slate-400">of {{ $trip->capacity }} passengers</div>
                  <div class="text-xs mt-1 {{ $utilizationPercentage >= 80 ? 'text-red-600 dark:text-red-400' : ($utilizationPercentage >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400') }}">
                    {{ $utilizationPercentage }}% capacity
                  </div>
                </div>
              </div>
            </div>
            
            @if($bookedTickets->count() > 0)
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
                  <thead class="bg-slate-100 dark:bg-slate-800">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passenger</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ticket Code</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Quantity</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ferry Pass</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Booked</th>
                      <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
                    @foreach($bookedTickets as $ticket)
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
                          <div class="text-sm font-mono text-slate-900 dark:text-slate-100">{{ $ticket->code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400">
                            {{ $ticket->quantity }} {{ $ticket->quantity == 1 ? 'person' : 'people' }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
                          ${{ number_format($ticket->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                          @if($ticket->pass_issued_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400">
                              ✅ Issued
                            </span>
                          @else
                            @if($ticket->hasValidBooking ?? false)
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400">
                                ✓ Eligible
                              </span>
                            @else
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400">
                                ✗ No Hotel
                              </span>
                            @endif
                          @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-slate-500 dark:text-slate-400">
                          {{ $ticket->created_at->format('M j, g:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                          <div class="flex items-center justify-center gap-2">
                            @if($ticket->pass_issued_at)
                              <a href="{{ route('manage.ferry.pass.view', $ticket->id) }}" target="_blank" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="View Ferry Pass">
                                🎫
                              </a>
                            @else
                              <span class="text-gray-400 dark:text-gray-600" title="Ferry pass not issued yet">🎫</span>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="p-12 text-center text-slate-500 dark:text-slate-400">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl mx-auto mb-4">🎫</div>
                <p class="font-medium">No passengers booked</p>
                <p class="text-xs mt-1">This trip has no confirmed bookings yet</p>
              </div>
            @endif
          </div>
        @empty
          <div class="text-center py-12">
            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl mx-auto mb-4">⛴️</div>
            <p class="text-slate-500 dark:text-slate-400 font-medium">No ferry trips found for {{ date('M j, Y', strtotime($selectedDate)) }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Try selecting a different date</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="grid gap-4 sm:grid-cols-3">
  <a href="{{ route('manage.ferry.validate.form') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/50 grid place-content-center text-xl">✅</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Validate Tickets</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Check passenger tickets</div>
      </div>
    </div>
  </a>

  <a href="{{ route('manage.ferry-trips.index') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-xl">📅</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Manage Schedule</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">View and edit ferry trips</div>
      </div>
    </div>
  </a>

  <a href="{{ route('manage.ferry.reports') }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-xl">📊</div>
      <div>
        <div class="font-semibold text-slate-900 dark:text-slate-100">Reports</div>
        <div class="text-xs text-slate-500 dark:text-slate-400">Trip and revenue reports</div>
      </div>
    </div>
  </a>
</div>

@endsection
