@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">🏨</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Island Hotels</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Discover luxury accommodations across our tropical paradise</p>
    </div>
  </div>
</div>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
  @foreach($hotels as $hotel)
    <a href="{{ route('hotels.show',$hotel) }}" class="group relative rounded-2xl p-0.5 bg-gradient-to-br from-indigo-500/40 via-fuchsia-500/30 to-teal-500/40 hover:from-indigo-500/60 hover:via-fuchsia-500/50 hover:to-teal-500/60 transition-all duration-300 shadow-md hover:shadow-xl transform hover:-translate-y-1">
      <div class="h-full w-full rounded-[1rem] bg-white/90 dark:bg-slate-900/80 backdrop-blur p-6 flex flex-col justify-between ring-1 ring-slate-200/60 dark:ring-slate-700/60">
        <div class="flex-1">
          <div class="flex items-start justify-between mb-3">
            <h2 class="text-lg font-semibold tracking-tight text-slate-800 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $hotel->name }}</h2>
            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-xs opacity-0 group-hover:opacity-100 transition">🏖️</div>
          </div>
          <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">{{ Str::limit($hotel->description, 110) }}</p>
          <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-500 mb-3">
            <span class="w-4 h-4 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center">📍</span>
            <span>{{ $hotel->address }}</span>
          </div>
        </div>
        
        <div class="mt-4 flex items-center justify-between text-xs border-t border-slate-200 dark:border-slate-700 pt-4">
          <span class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8m-4-4v4m8-10c0 5.523-4.477 10-10 10S2 16.523 2 11 6.477 1 12 1s10 4.477 10 10ZM9 9.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1Zm6 0a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1Z"/>
            </svg>
            View Rooms & Prices
          </span>
          <span class="text-indigo-600 dark:text-indigo-400 font-medium opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition-all">Explore →</span>
        </div>
      </div>
    </a>
  @endforeach
</div>
<div class="mt-6">{{ $hotels->links() }}</div>
@endsection
