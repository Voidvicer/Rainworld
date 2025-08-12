@extends('layouts.app')
@section('content')
<h3>My Activity Bookings</h3>
<table class="table table-striped">
  <thead><tr><th>Activity</th><th>Date</th><th>Time</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead>
  <tbody>
  @foreach($bookings as $b)
    <tr>
      <td>{{ $b->schedule->activity->name }}</td>
      <td>{{ $b->schedule->date }}</td>
      <td>{{ $b->schedule->start_time }}-{{ $b->schedule->end_time }}</td>
      <td>{{ $b->quantity }}</td>
      <td>${{ number_format($b->total_amount,2) }}</td>
      <td>{{ $b->status }}</td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $bookings->links() }}
@endsection
