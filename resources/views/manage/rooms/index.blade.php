@extends('layouts.app')
@section('content')
<h3>Rooms</h3>
<a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary mb-2">New Room</a>
<table class="table table-bordered">
  <thead><tr><th>Hotel</th><th>Name</th><th>Type</th><th>Capacity</th><th>Total</th><th>Price</th><th></th></tr></thead>
  <tbody>
  @foreach($rooms as $r)
    <tr>
      <td>{{ $r->hotel->name }}</td>
      <td>{{ $r->name }}</td>
      <td>{{ $r->type }}</td>
      <td>{{ $r->capacity }}</td>
      <td>{{ $r->total_rooms }}</td>
      <td>${{ number_format($r->price_per_night,2) }}</td>
      <td><a href="{{ route('rooms.edit',$r) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $rooms->links() }}
@endsection
