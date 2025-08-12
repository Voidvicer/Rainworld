@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">🎯</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Activities & Events</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Adventure awaits with thrilling activities across the island</p>
    </div>
  </div>
  <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur rounded-xl p-4 border border-slate-200 dark:border-slate-700">
    <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
      <span class="w-5 h-5 rounded bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 grid place-content-center text-xs">⚠️</span>
      <span><strong>Park ticket required:</strong> You must have a valid theme park ticket to book activities</span>
    </div>
  </div>
</div>

<div class="space-y-8">
@foreach($activities as $a)
  <div class="group relative rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden transition-all hover:shadow-xl">
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-5 border-b border-slate-200 dark:border-slate-600">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex-1">
          <div class="flex items-center gap-3 mb-2">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">{{ $a->name }}</h2>
            <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize {{ $a->type === 'adventure' ? 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-400' : 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400' }}">{{ $a->type }}</span>
          </div>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-3xl">{{ $a->description }}</p>
        </div>
        <div class="text-right">
          <div class="text-xs text-slate-500 dark:text-slate-500 uppercase tracking-wide">Base Price</div>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($a->base_price,2) }}</div>
        </div>
      </div>
    </div>
    
    <div class="p-6">
      <div class="overflow-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">Date</th>
              <th class="px-4 py-3 text-left font-semibold">Time Slot</th>
              <th class="px-4 py-3 text-center font-semibold">Available</th>
              <th class="px-4 py-3 text-right font-semibold">Price</th>
              <th class="px-4 py-3 text-center font-semibold">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($a->schedules as $s)
              <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors">
                <td class="px-4 py-3 text-slate-700 dark:text-slate-300 font-medium">{{ $s->date }}</td>
                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                  <span class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs font-mono">{{ $s->start_time }}-{{ $s->end_time }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span class="font-semibold {{ $s->remaining() < 5 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $s->remaining() }}</span>
                  <span class="text-xs text-slate-500 dark:text-slate-500 ml-1">spots</span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-lg text-slate-800 dark:text-slate-200">${{ number_format($a->base_price,2) }}</td>
                <td class="px-4 py-3 text-center">
                  <a class="inline-flex items-center gap-1 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow transition-all transform hover:scale-[1.02] active:scale-[0.98]" href="{{ route('activity.book.create',$s) }}">
                    <span>Book Now</span>
                    <span>→</span>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-8 text-center">
                  <div class="flex flex-col items-center gap-2">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-xl">📅</div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">No schedules available</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endforeach
</div>
<div class="mt-6">{{ $activities->links() }}</div>
@endsection
