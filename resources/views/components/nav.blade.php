<nav x-data="{open:false}" class="gradient-header text-white shadow relative">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-6">
      <a href="{{ route('home') }}" class="font-semibold tracking-wide text-lg flex items-center gap-2">
        <span>🏝️</span><span>Rainworld</span>
      </a>
      <button @click="open=!open" class="md:hidden focus:outline-none focus:ring-2 focus:ring-white rounded px-2 py-1 bg-white/10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      </button>
      <div class="hidden md:flex gap-1 text-sm font-medium">
        @auth
          @php
            $nav = [["Hotels","hotels.index"],["Ferry","ferry.trips.index"],["Bookings","bookings.index"]];
          @endphp
          @foreach($nav as $item)
            <a href="{{ route($item[1]) }}" class="px-3 py-1 rounded transition {{ request()->routeIs($item[1]) ? 'bg-white/20' : 'hover:bg-white/10' }}">{{ $item[0] }}</a>
          @endforeach
          @role('hotel_manager|admin')<a href="{{ route('manage.hotel.dashboard') }}" class="px-3 py-1 rounded transition {{ request()->routeIs('manage.hotel.*') || request()->routeIs('manage.bookings') ? 'bg-white/20':'hover:bg-white/10' }}">Hotel Mgmt</a>@endrole
          @role('ferry_staff|admin')<a href="{{ route('manage.ferry.dashboard') }}" class="px-3 py-1 rounded transition {{ request()->routeIs('manage.ferry*') ? 'bg-white/20':'hover:bg-white/10' }}">Ferry Ops</a>@endrole
          @role('admin')<a href="{{ route('admin.index') }}" class="px-3 py-1 rounded transition {{ request()->routeIs('admin.*') ? 'bg-white/20':'hover:bg-white/10' }}">Admin</a>@endrole
        @endauth
      </div>
    </div>
    <div class="flex items-center gap-3">
      @auth
        <span class="hidden sm:inline text-xs uppercase tracking-wide bg-white/10 px-2 py-1 rounded-full">{{ implode(',', auth()->user()->getRoleNames()->toArray()) }}</span>
        <span class="font-medium">Hi, {{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold px-3 py-1 rounded transition">Logout</button></form>
      @else
        <a href="{{ route('login') }}" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded transition">Login</a>
        <a href="{{ route('register') }}" class="bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold px-3 py-1 rounded transition">Register</a>
      @endauth
    </div>
  </div>
  <div x-cloak x-show="open" x-transition.origin.top.left class="md:hidden border-t border-white/10 bg-slate-900/90 backdrop-blur px-4 pb-4 space-y-2">
      @auth
        @foreach($nav as $item)
          <a href="{{ route($item[1]) }}" class="block px-3 py-2 rounded {{ request()->routeIs($item[1]) ? 'bg-white/15' : 'hover:bg-white/10' }}">{{ $item[0] }}</a>
        @endforeach
        @role('hotel_manager|admin')<a href="{{ route('manage.hotel.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('manage.hotel.*') || request()->routeIs('manage.bookings') ? 'bg-white/15':'hover:bg-white/10' }}">Hotel Management</a>@endrole
        @role('ferry_staff|admin')<a href="{{ route('manage.ferry.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('manage.ferry*') ? 'bg-white/15':'hover:bg-white/10' }}">Ferry Operations</a>@endrole
        @role('admin')<a href="{{ route('admin.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.*') ? 'bg-white/15':'hover:bg-white/10' }}">Admin</a>@endrole
      @else
        <a href="{{ route('login') }}" class="block px-3 py-2 rounded hover:bg-white/10">Login</a>
        <a href="{{ route('register') }}" class="block px-3 py-2 rounded hover:bg-white/10">Register</a>
      @endauth
  </div>
</nav>
