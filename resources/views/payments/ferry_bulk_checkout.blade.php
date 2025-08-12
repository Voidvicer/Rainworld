@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">⛴️</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Ferry Tickets Payment</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Complete your ferry reservation</p>
    </div>
  </div>
</div>

<div class="max-w-2xl mx-auto">
  <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
      <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
        <span class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-sm">📋</span>
        Booking Summary
      </h2>
    </div>
    
    <div class="p-6 space-y-6">
      <div class="grid gap-4">
        <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700">
          <span class="text-slate-600 dark:text-slate-400">Travel Date:</span>
          <span class="font-medium text-slate-800 dark:text-slate-200">{{ $bookingData['date'] }}</span>
        </div>
        
        <div class="space-y-4">
          <h3 class="font-semibold text-slate-800 dark:text-slate-200">Selected Trips:</h3>
          @foreach($bookingData['trips'] as $trip)
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
              <div class="flex justify-between items-start mb-2">
                <div>
                  <span class="px-2 py-1 {{ $trip['type'] == 'departure' ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400' }} rounded text-xs font-semibold">
                    {{ ucfirst($trip['type']) }}
                  </span>
                  <span class="ml-2 font-mono bg-white dark:bg-slate-900 rounded-md px-2 py-1 text-sm">{{ $trip['time'] }}</span>
                </div>
                <div class="text-right">
                  <div class="font-medium text-slate-800 dark:text-slate-200">{{ $trip['quantity'] }} × ${{ number_format($trip['price'], 2) }}</div>
                  <div class="text-sm text-slate-500 dark:text-slate-500">${{ number_format($trip['price'] * $trip['quantity'], 2) }}</div>
                </div>
              </div>
              <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                <span class="font-medium">{{ $trip['origin'] }}</span>
                <span class="text-slate-400 dark:text-slate-500">→</span>
                <span class="font-medium">{{ $trip['destination'] }}</span>
              </div>
            </div>
          @endforeach
        </div>
        
        <div class="flex justify-between items-center py-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg px-4">
          <span class="font-semibold text-slate-800 dark:text-slate-200">Total Amount:</span>
          <span class="font-bold text-2xl text-emerald-600 dark:text-emerald-400">${{ number_format($total, 2) }}</span>
        </div>
      </div>

      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400 text-sm">
          <span class="w-5 h-5 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-xs">ℹ️</span>
          <span>Mock payment: Click confirm to finalize your ferry bookings</span>
        </div>
      </div>

      <form method="POST" action="{{ route('ferry.tickets.bulk.store') }}" class="space-y-4">@csrf
        <div class="flex gap-3">
          <button class="btn-primary flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-md transition-all transform hover:scale-[1.02] active:scale-[0.98]">
            Confirm & Pay
          </button>
          <a href="{{ route('ferry.trips.index') }}" class="px-6 py-2 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg transition text-center">
            Modify
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
