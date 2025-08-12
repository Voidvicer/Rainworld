@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Ferry Ticket Validation</h1>
<form method="POST" action="{{ route('manage.ferry.validate.check') }}" class="max-w-lg flex gap-3" onsubmit="if(!this.code.value.trim()) return false;">@csrf
  <input type="text" name="code" class="flex-1 rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter ticket code" autofocus>
  <button class="btn-primary">Validate</button>
</form>
@if(session('result'))
  @php($r=session('result'))
  <div class="mt-6 p-4 rounded-xl border {{ $r['valid'] ? 'border-emerald-300 bg-emerald-50 text-emerald-700':'border-rose-300 bg-rose-50 text-rose-700' }}">
    <div class="font-semibold mb-2 text-sm tracking-wide">{{ $r['valid'] ? 'VALID TICKET' : 'INVALID TICKET' }}</div>
    <pre class="text-xs overflow-auto max-h-64">{{ json_encode($r, JSON_PRETTY_PRINT) }}</pre>
  </div>
@endif
@endsection
