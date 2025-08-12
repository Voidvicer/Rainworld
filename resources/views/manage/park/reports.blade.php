@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Theme Park Ticket Sales</h1>
<div class="overflow-auto rounded-xl shadow ring-1 ring-slate-200 bg-white/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
      <tr>
        <th class="px-4 py-2 text-left">Code</th>
        <th class="px-4 py-2 text-left">User</th>
        <th class="px-4 py-2 text-left">Date</th>
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
          <td class="px-4 py-2">{{ $t->visit_date }}</td>
          <td class="px-4 py-2 text-center">{{ $t->quantity }}</td>
          <td class="px-4 py-2 text-right font-medium">${{ number_format($t->total_amount,2) }}</td>
          <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold 
            @if($t->status==='confirmed') bg-emerald-100 text-emerald-700
            @elseif($t->status==='canceled') bg-rose-100 text-rose-700
            @elseif($t->status==='completed') bg-indigo-100 text-indigo-700
            @elseif($t->status==='expired') bg-gray-100 text-gray-700
            @else bg-amber-100 text-amber-700 @endif">{{ $t->status }}</span></td>
          <td class="px-4 py-2">
            <form method="POST" action="{{ route('manage.park.tickets.status',$t) }}" class="flex items-center gap-2">@csrf @method('PATCH')
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
