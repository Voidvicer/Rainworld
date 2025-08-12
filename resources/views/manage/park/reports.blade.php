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
          <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold {{ $t->status==='paid' ? 'bg-emerald-100 text-emerald-700':'bg-slate-200 text-slate-700' }}">{{ $t->status }}</span></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $tickets->links() }}</div>
@endsection
