@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Hotel Booking Management</h1>
<div class="overflow-auto rounded-xl shadow ring-1 ring-slate-200 bg-white/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
      <tr>
        <th class="px-4 py-2 text-left">Code</th>
        <th class="px-4 py-2 text-left">User</th>
        <th class="px-4 py-2 text-left">Hotel / Room</th>
        <th class="px-4 py-2 text-left">Dates</th>
        <th class="px-4 py-2">Status</th>
        <th class="px-4 py-2">Update</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @foreach($bookings as $b)
        <tr class="hover:bg-indigo-50/40">
          <td class="px-4 py-2 font-mono text-xs">{{ $b->confirmation_code }}</td>
          <td class="px-4 py-2">{{ $b->user->email }}</td>
          <td class="px-4 py-2">{{ $b->room->hotel->name }} / {{ $b->room->name }}</td>
          <td class="px-4 py-2">{{ $b->check_in }} → {{ $b->check_out }}</td>
          <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold
            @if($b->status==='confirmed') bg-emerald-100 text-emerald-700
            @elseif($b->status==='canceled') bg-rose-100 text-rose-700
            @elseif($b->status==='completed') bg-indigo-100 text-indigo-700
            @else bg-amber-100 text-amber-700 @endif">{{ $b->status }}</span></td>
          <td class="px-4 py-2">
            <form method="POST" action="{{ route('manage.bookings.status',$b) }}" class="flex items-center gap-2">@csrf @method('PATCH')
              <select name="status" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                @foreach(['pending','confirmed','canceled','completed'] as $s)
                  <option value="{{ $s }}" @if($b->status==$s) selected @endif>{{ ucfirst($s) }}</option>
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
<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
