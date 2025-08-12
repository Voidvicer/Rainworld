@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Manage Promotions</h1>
<form method="POST" action="{{ route('admin.ads.store') }}" class="bg-white/70 backdrop-blur ring-1 ring-slate-200 rounded-xl p-6 shadow space-y-5">@csrf
  <div class="grid md:grid-cols-2 gap-4">
    <label class="space-y-1 text-sm font-medium text-slate-600">
      <span>Title</span>
      <input name="title" required class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="Promo title">
    </label>
    <label class="space-y-1 text-sm font-medium text-slate-600">
      <span>Image URL</span>
      <input name="image_url" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="https://...">
    </label>
  </div>
  <label class="space-y-1 text-sm font-medium text-slate-600 block">
    <span>Content</span>
    <textarea name="content" rows="3" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="Details..."></textarea>
  </label>
  <div class="grid md:grid-cols-4 gap-4 text-sm">
    <label class="space-y-1 font-medium text-slate-600">
      <span>Starts</span>
      <input type="date" name="starts_at" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full">
    </label>
    <label class="space-y-1 font-medium text-slate-600">
      <span>Ends</span>
      <input type="date" name="ends_at" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full">
    </label>
    <label class="space-y-1 font-medium text-slate-600">
      <span>Scope</span>
      <select name="scope" class="rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 w-full">
        <option value="global">Global</option>
        <option value="hotel">Hotel</option>
        <option value="ferry">Ferry</option>
        <option value="park">Park</option>
      </select>
    </label>
    <label class="flex items-end gap-2 mt-5">
      <input type="checkbox" name="active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
      <span class="text-sm font-medium text-slate-600">Active</span>
    </label>
  </div>
  <div class="flex justify-end">
    <button class="btn-primary">Save Promotion</button>
  </div>
</form>

<div class="mt-8 overflow-auto rounded-xl shadow ring-1 ring-slate-200 bg-white/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide">
      <tr>
        <th class="px-4 py-2 text-left">Title</th>
        <th class="px-4 py-2 text-left">Scope</th>
        <th class="px-4 py-2 text-left">Period</th>
        <th class="px-4 py-2">Active</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      @foreach($promos as $p)
        <tr class="hover:bg-indigo-50/40">
          <td class="px-4 py-2 font-medium">{{ $p->title }}</td>
          <td class="px-4 py-2 capitalize">{{ $p->scope }}</td>
          <td class="px-4 py-2 text-xs">{{ $p->starts_at }} → {{ $p->ends_at }}</td>
          <td class="px-4 py-2 text-center"><span class="px-2 py-1 rounded-full text-xs font-semibold {{ $p->active?'bg-emerald-100 text-emerald-700':'bg-slate-200 text-slate-600' }}">{{ $p->active?'Yes':'No' }}</span></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $promos->links() }}</div>
@endsection
