@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-xl">📢</div>
        <div>
          <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Content Management System</h1>
          <p class="text-sm text-slate-600 dark:text-slate-400">Manage promotions and advertisements across the platform</p>
        </div>
      </div>
    </div>
    
    <div class="p-6">
      <form method="POST" action="{{ route('admin.ads.store') }}" class="space-y-6">
        @csrf
        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Title *</label>
            <input type="text" id="title" name="title" required
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="Enter promotion title">
          </div>
          
          <div>
            <label for="image_url" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Image URL</label>
            <input type="url" id="image_url" name="image_url"
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="https://example.com/image.jpg">
          </div>
        </div>
        
        <div>
          <label for="content" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Content</label>
          <textarea id="content" name="content" rows="4"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Enter promotion details and description..."></textarea>
        </div>
        
        <div class="grid gap-6 md:grid-cols-4">
          <div>
            <label for="discount_percentage" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Discount %</label>
            <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01"
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="0.00">
          </div>
          
          <div>
            <label for="starts_at" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Start Date</label>
            <input type="date" id="starts_at" name="starts_at"
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          
          <div>
            <label for="ends_at" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">End Date</label>
            <input type="date" id="ends_at" name="ends_at"
                   class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          
          <div>
            <label for="scope" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Scope</label>
            <select id="scope" name="scope" required
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
              <option value="global">Global (All Areas)</option>
              <option value="hotel">Hotel Services</option>
              <option value="ferry">Ferry Services</option>
              <option value="park">Theme Park</option>
            </select>
          </div>
        </div>
        
        <div class="flex items-center gap-3">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="active" checked
                   class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Active Promotion</span>
          </label>
        </div>
        
        <div class="flex justify-end pt-4 border-t border-slate-200 dark:border-slate-600">
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
            Create Promotion
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Promotions List -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Active Promotions</h2>
  </div>
  
  <div class="overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
      <thead class="bg-slate-50 dark:bg-slate-800">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Promotion</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Scope</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Discount</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Period</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($promos as $promo)
          <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                @if($promo->image_url)
                  <img src="{{ $promo->image_url }}" alt="{{ $promo->title }}" class="w-12 h-12 rounded-lg object-cover">
                @else
                  <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 grid place-content-center text-white font-semibold">
                    {{ strtoupper(substr($promo->title, 0, 1)) }}
                  </div>
                @endif
                <div>
                  <div class="font-medium text-slate-900 dark:text-slate-100">{{ $promo->title }}</div>
                  @if($promo->content)
                    <div class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ Str::limit($promo->content, 60) }}</div>
                  @endif
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($promo->scope === 'global') bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-400
                @elseif($promo->scope === 'hotel') bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400
                @elseif($promo->scope === 'ferry') bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400
                @else bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 @endif">
                {{ ucfirst($promo->scope) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              @if($promo->discount_percentage)
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $promo->discount_percentage }}% OFF</span>
              @else
                <span class="text-slate-400 dark:text-slate-500">No discount</span>
              @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
              @if($promo->starts_at && $promo->ends_at)
                {{ $promo->starts_at->format('M j') }} - {{ $promo->ends_at->format('M j, Y') }}
              @elseif($promo->starts_at)
                From {{ $promo->starts_at->format('M j, Y') }}
              @elseif($promo->ends_at)
                Until {{ $promo->ends_at->format('M j, Y') }}
              @else
                No time limit
              @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $promo->active ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                {{ $promo->active ? 'Active' : 'Inactive' }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">📢</div>
                <div>
                  <p class="font-medium">No promotions created yet</p>
                  <p class="text-xs">Create your first promotion using the form above</p>
                </div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  @if($promos->hasPages())
    <div class="p-6 border-t border-slate-200 dark:border-slate-600">
      {{ $promos->links() }}
    </div>
  @endif
</div>
@endsection
