@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Island Map Content</h1>
<form method="POST" action="{{ route('admin.map.store') }}" class="bg-white/70 dark:bg-slate-800/70 backdrop-blur ring-1 ring-slate-200 dark:ring-slate-700 rounded-xl p-6 shadow space-y-5">@csrf
  <div class="grid md:grid-cols-3 gap-4">
    <label class="space-y-1 text-sm font-medium text-slate-600 dark:text-slate-300"><span>Name</span><input name="name" required class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="Location name"></label>
    <label class="space-y-1 text-sm font-medium text-slate-600 dark:text-slate-300"><span>Latitude</span><input name="lat" required class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="4.1753"></label>
    <label class="space-y-1 text-sm font-medium text-slate-600 dark:text-slate-300"><span>Longitude</span><input name="lng" required class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="73.5093"></label>
  </div>
  <label class="space-y-1 text-sm font-medium text-slate-600 dark:text-slate-300 block"><span>Description</span><textarea name="description" rows="3" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="Details..."></textarea></label>
  <div class="grid md:grid-cols-4 gap-4">
    <label class="space-y-1 text-sm font-medium text-slate-600 dark:text-slate-300 md:col-span-2"><span>Category</span><input name="category" class="rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 w-full" placeholder="hotel / beach / transport"></label>
    <label class="flex items-end gap-2 md:col-span-2">
      <input type="hidden" name="active" value="0">
      <input type="checkbox" name="active" value="1" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500 bg-white dark:bg-slate-700" checked>
      <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Active</span>
    </label>
  </div>
  <div class="flex justify-end"><button class="bg-gradient-to-r from-indigo-600 to-blue-700 hover:from-indigo-700 hover:to-blue-800 text-white px-6 py-2 rounded-lg font-medium shadow-lg hover:shadow-xl transition-all duration-200">Save Location</button></div>
</form>

<div class="mt-8 overflow-auto rounded-xl shadow ring-1 ring-slate-200 dark:ring-slate-700 bg-white/70 dark:bg-slate-800/70">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs uppercase tracking-wide">
      <tr><th class="px-4 py-2 text-left">Name</th><th class="px-4 py-2 text-left">Lat</th><th class="px-4 py-2 text-left">Lng</th><th class="px-4 py-2 text-left">Category</th><th class="px-4 py-2 text-center">Active</th><th class="px-4 py-2 text-center">Actions</th></tr>
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
      @foreach($locations as $l)
        <tr class="hover:bg-indigo-50/40 dark:hover:bg-slate-700/40 text-slate-900 dark:text-slate-100">
          <td class="px-4 py-2 font-medium">{{ $l->name }}</td>
          <td class="px-4 py-2">{{ $l->lat }}</td>
          <td class="px-4 py-2">{{ $l->lng }}</td>
          <td class="px-4 py-2">{{ $l->category }}</td>
          <td class="px-4 py-2 text-center">
            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $l->active?'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300':'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-300' }}">{{ $l->active?'Yes':'No' }}</span>
          </td>
          <td class="px-4 py-2 text-center">
            <div class="flex items-center justify-center gap-2">
              <!-- Toggle Active/Inactive -->
              <form method="POST" action="{{ route('admin.map.toggle', $l->id) }}" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-3 py-1 rounded text-xs font-medium transition-colors {{ $l->active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/70' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/70' }}" 
                        title="{{ $l->active ? 'Deactivate' : 'Activate' }}">
                  {{ $l->active ? 'Deactivate' : 'Activate' }}
                </button>
              </form>
              
              <!-- Delete -->
              <form method="POST" action="{{ route('admin.map.delete', $l->id) }}" class="inline" 
                    onsubmit="return confirm('Are you sure you want to delete this location?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 rounded text-xs font-medium bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/50 dark:text-red-300 dark:hover:bg-red-900/70 transition-colors" 
                        title="Delete">
                  Delete
                </button>
              </form>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $locations->links() }}</div>
@endsection
