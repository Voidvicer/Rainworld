@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
  <div class="rounded-xl p-5 bg-gradient-to-br from-indigo-600 to-teal-600 text-white shadow">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Users</div>
    <div class="text-3xl font-bold">{{ $stats['users'] }}</div>
  </div>
  <div class="rounded-xl p-5 bg-gradient-to-br from-amber-500 to-pink-500 text-white shadow">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Hotel Bookings</div>
    <div class="text-3xl font-bold">{{ $stats['hotel_bookings'] }}</div>
  </div>
  <div class="rounded-xl p-5 bg-gradient-to-br from-sky-500 to-indigo-500 text-white shadow">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Ferry Tickets</div>
    <div class="text-3xl font-bold">{{ $stats['ferry_tickets'] }}</div>
  </div>
  <div class="rounded-xl p-5 bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow">
    <div class="text-xs uppercase tracking-wide opacity-80 mb-1">Park Tickets</div>
    <div class="text-3xl font-bold">{{ $stats['park_tickets'] }}</div>
  </div>
</div>
@endsection
