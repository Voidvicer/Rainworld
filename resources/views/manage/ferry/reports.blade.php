@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Ferry Ticket Reports</h1>
<div class="mb-8 overflow-auto rounded-xl shadow ring-1 ring-slate-200 bg-white/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-600">
      <tr>
        <th class="px-3 py-2 text-left">Date</th>
        <th class="px-3 py-2 text-left">Time</th>
        <th class="px-3 py-2 text-left">Route</th>
        <th class="px-3 py-2">Capacity</th>
        <th class="px-3 py-2">Remaining</th>
        <th class="px-3 py-2">Blocked?</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @foreach(\App\Models\FerryTrip::orderBy('date')->orderBy('depart_time')->limit(50)->get() as $trip)
        <tr class="hover:bg-indigo-50/30">
          <td class="px-3 py-2">{{ $trip->date }}</td>
          <td class="px-3 py-2 font-mono text-xs">{{ $trip->depart_time }}</td>
          <td class="px-3 py-2">{{ $trip->origin }} → {{ $trip->destination }}</td>
          <td class="px-3 py-2 text-center">{{ $trip->capacity }}</td>
          <td class="px-3 py-2 text-center">{{ $trip->remainingSeats() }}</td>
          <td class="px-3 py-2 text-center">
            @if($trip->blocked)
              <span class="px-2 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">Blocked</span>
            @else
              <span class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Open</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="overflow-auto rounded-xl shadow ring-1 ring-slate-200 bg-white/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
      <tr>
        <th class="px-4 py-2 text-left">Code</th>
        <th class="px-4 py-2 text-left">User</th>
        <th class="px-4 py-2 text-left">Trip</th>
        <th class="px-4 py-2">Qty</th>
        <th class="px-4 py-2 text-right">Total</th>
        <th class="px-4 py-2">Status</th>
        <th class="px-4 py-2">Update</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @foreach($tickets as $t)
        <tr class="hover:bg-indigo-50/40">
          <td class="px-4 py-2 font-mono text-xs">{{ $t->code }}</td>
          <td class="px-4 py-2">{{ $t->user->email }}</td>
          <td class="px-4 py-2">{{ $t->trip->date }} {{ $t->trip->depart_time }} ({{ $t->trip->origin }}→{{ $t->trip->destination }})</td>
          <td class="px-4 py-2 text-center">{{ $t->quantity }}</td>
          <td class="px-4 py-2 text-right font-medium">${{ number_format($t->total_amount,2) }}</td>
          <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold 
            @if($t->status==='confirmed') bg-emerald-100 text-emerald-700
            @elseif($t->status==='canceled') bg-rose-100 text-rose-700
            @elseif($t->status==='completed') bg-indigo-100 text-indigo-700
            @elseif($t->status==='expired') bg-gray-100 text-gray-700
            @else bg-amber-100 text-amber-700 @endif">{{ $t->status }}</span></td>
          <td class="px-4 py-2">
            <form method="POST" action="{{ route('manage.ferry.tickets.status',$t) }}" class="flex items-center gap-2">@csrf @method('PATCH')
              <select name="status" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                @foreach(['confirmed','canceled','completed','expired'] as $s)
                  <option value="{{ $s }}" @if($t->status==$s) selected @endif>{{ ucfirst($s) }}</option>
                @endforeach
              </select>
              <button class="text-[11px] font-semibold px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-500 text-white">Save</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
