@extends('layouts.app')
@section('content')
<h3>New Room</h3>
<form method="POST" action="{{ route('rooms.store') }}">@csrf
  <div class="mb-2">
    <label class="form-label">Hotel</label>
    <select name="hotel_id" class="form-select">
      @foreach($hotels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
    </select>
  </div>
  <div class="row g-2">
    <div class="col"><input class="form-control" name="name" placeholder="Name" required></div>
    <div class="col"><input class="form-control" name="type" placeholder="Type" required></div>
  </div>
  <div class="row g-2 mt-2">
    <div class="col"><input type="number" class="form-control" name="capacity" placeholder="Capacity" min="1" required></div>
    <div class="col"><input type="number" class="form-control" name="total_rooms" placeholder="Total rooms" min="1" required></div>
    <div class="col"><input type="number" step="0.01" class="form-control" name="price_per_night" placeholder="Price" required></div>
  </div>
  <button class="btn btn-primary mt-3">Save</button>
</form>
@endsection
