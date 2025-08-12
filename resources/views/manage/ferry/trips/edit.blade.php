@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Ferry Trip</h1>
<form method="POST" action="{{ route('manage.ferry-trips.update',$trip) }}" class="max-w-xl space-y-5 bg-white rounded-xl shadow p-6 ring-1 ring-slate-200">@csrf @method('PUT')
  <div class="grid grid-cols-2 gap-4">
    <div><label class="label">Date</label><input type="date" name="date" value="{{ $trip->date }}" class="input" required></div>
    <div><label class="label">Depart Time</label><input type="time" name="depart_time" value="{{ $trip->depart_time }}" class="input" required></div>
    <div><label class="label">Origin</label><input type="text" name="origin" value="{{ $trip->origin }}" class="input" required></div>
    <div><label class="label">Destination</label><input type="text" name="destination" value="{{ $trip->destination }}" class="input" required></div>
    <div><label class="label">Capacity</label><input type="number" name="capacity" value="{{ $trip->capacity }}" min="1" class="input" required></div>
    <div><label class="label">Price</label><input type="number" step="0.01" name="price" value="{{ $trip->price }}" class="input" required></div>
  </div>
  <label class="inline-flex items-center gap-2"><input type="checkbox" name="blocked" value="1" class="rounded border-slate-300" {{ $trip->blocked ? 'checked' : '' }}> <span class="text-sm text-slate-700">Blocked (hide from visitors)</span></label>
  <div class="pt-2 flex gap-3 items-center">
    <button class="btn-primary">Update Trip</button>
    <a href="{{ route('manage.ferry-trips.index') }}" class="text-sm text-slate-600 hover:text-slate-800">Back</a>
  </div>
</form>
@endsection
