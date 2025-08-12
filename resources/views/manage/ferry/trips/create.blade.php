@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Create Ferry Trip</h1>
<form method="POST" action="{{ route('manage.ferry-trips.store') }}" class="max-w-xl space-y-5 bg-white rounded-xl shadow p-6 ring-1 ring-slate-200">@csrf
  <div class="grid grid-cols-2 gap-4">
    <div><label class="label">Date</label><input type="date" name="date" class="input" required></div>
    <div><label class="label">Depart Time</label><input type="time" name="depart_time" class="input" required></div>
    <div><label class="label">Origin</label><input type="text" name="origin" value="Male' City" class="input" required></div>
    <div><label class="label">Destination</label><input type="text" name="destination" value="Picnic Island" class="input" required></div>
    <div><label class="label">Capacity</label><input type="number" name="capacity" value="50" min="1" class="input" required></div>
    <div><label class="label">Price</label><input type="number" step="0.01" name="price" value="15" class="input" required></div>
  </div>
  <label class="inline-flex items-center gap-2"><input type="checkbox" name="blocked" value="1" class="rounded border-slate-300"> <span class="text-sm text-slate-700">Blocked (hide from visitors)</span></label>
  <div class="pt-2">
    <button class="btn-primary">Save Trip</button>
  </div>
</form>
@endsection
