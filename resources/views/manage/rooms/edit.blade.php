@extends('layouts.app')
@section('content')
<h3>Edit Room</h3>
<form method="POST" action="{{ route('rooms.update',$room) }}">@csrf @method('PUT')
  <div class="mb-2">
    <label class="form-label">Hotel</label>
    <select name="hotel_id" class="form-select">
      @foreach($hotels as $h)<option value="{{ $h->id }}" @if($room->hotel_id==$h->id) selected @endif>{{ $h->name }}</option>@endforeach
    </select>
  </div>
  <div class="row g-2">
    <div class="col"><input class="form-control" name="name" value="{{ $room->name }}" required></div>
    <div class="col"><input class="form-control" name="type" value="{{ $room->type }}" required></div>
  </div>
  <div class="row g-2 mt-2">
    <div class="col"><input type="number" class="form-control" name="capacity" value="{{ $room->capacity }}" min="1" required></div>
    <div class="col"><input type="number" class="form-control" name="total_rooms" value="{{ $room->total_rooms }}" min="1" required></div>
    <div class="col"><input type="number" step="0.01" class="form-control" name="price_per_night" value="{{ $room->price_per_night }}" required></div>
  </div>
  <button class="btn btn-primary mt-3">Save</button>
</form>
@endsection
