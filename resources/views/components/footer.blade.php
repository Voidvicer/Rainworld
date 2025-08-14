<footer class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white">
  <!-- Top Border -->
  <div class="border-t border-slate-700"></div>
  
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-12">
    <div class="grid gap-8 md:grid-cols-3">
      <!-- Brand & Description -->
      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 grid place-content-center text-white">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
          </div>
          <div>
            <div class="text-lg font-bold text-white">Rainworld Picnic Island</div>
            <div class="text-sm text-slate-300">Premium Island Resort</div>
          </div>
        </div>
        <p class="text-sm text-slate-400 leading-relaxed">
          Experience luxury and adventure at our exclusive island resort. Where pristine beaches meet world-class hospitality.
        </p>
        <div class="text-xs text-slate-500">&copy; {{ date('Y') }} Rainworld Picnic Island Resort. All rights reserved.</div>
      </div>

      <!-- Contact Information -->
      <div class="space-y-4">
        <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Contact Information</h3>
        <div class="space-y-3 text-sm">
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded bg-slate-700 grid place-content-center">
              <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-4h2v4h2v-4h2v6z"/>
              </svg>
            </div>
            <div>
              <div class="text-slate-300">Rainworld Resort Management</div>
              <div class="text-slate-400 text-xs">Luxury Island Hospitality</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded bg-slate-700 grid place-content-center">
              <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
              </svg>
            </div>
            <div>
              <div class="text-slate-300">reservations@rainworld-island.com</div>
              <div class="text-slate-400 text-xs">24/7 Guest Services</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded bg-slate-700 grid place-content-center">
              <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
              </svg>
            </div>
            <div>
              <div class="text-slate-300">+960 (555) 678-9012</div>
              <div class="text-slate-400 text-xs">International Bookings</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-5 h-5 rounded bg-slate-700 grid place-content-center">
              <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
              </svg>
            </div>
            <div>
              <div class="text-slate-300">Picnic Island, North Malé Atoll</div>
              <div class="text-slate-400 text-xs">Republic of Maldives</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Social Media & Newsletter -->
      <div class="space-y-4">
        <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Stay Connected</h3>
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="group flex items-center gap-3 p-3 rounded-lg bg-slate-800/50 hover:bg-gradient-to-r hover:from-pink-500/20 hover:to-purple-500/20 transition-all duration-300 border border-slate-700 hover:border-pink-500/30">
              <div class="w-6 h-6 rounded bg-gradient-to-br from-pink-500 to-purple-600 grid place-content-center text-white">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8A1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5a5 5 0 0 1-5 5a5 5 0 0 1-5-5a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3z"/>
                </svg>
              </div>
              <div class="text-sm">
                <div class="text-slate-300 group-hover:text-white">Instagram</div>
                <div class="text-xs text-slate-500">@rainworld_island</div>
              </div>
            </a>
          </div>
          <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="group flex items-center gap-3 p-3 rounded-lg bg-slate-800/50 hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-cyan-500/20 transition-all duration-300 border border-slate-700 hover:border-blue-500/30">
              <div class="w-6 h-6 rounded bg-gradient-to-br from-blue-500 to-cyan-600 grid place-content-center text-white">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </div>
              <div class="text-sm">
                <div class="text-slate-300 group-hover:text-white">Facebook</div>
                <div class="text-xs text-slate-500">Rainworld Resort</div>
              </div>
            </a>
          </div>
          <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" class="group flex items-center gap-3 p-3 rounded-lg bg-slate-800/50 hover:bg-gradient-to-r hover:from-sky-500/20 hover:to-blue-500/20 transition-all duration-300 border border-slate-700 hover:border-sky-500/30">
              <div class="w-6 h-6 rounded bg-gradient-to-br from-sky-500 to-blue-600 grid place-content-center text-white">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
              </div>
              <div class="text-sm">
                <div class="text-slate-300 group-hover:text-white">Twitter</div>
                <div class="text-xs text-slate-500">@rainworld_resort</div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bottom Section -->
    <div class="mt-8 pt-6 border-t border-slate-700">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-xs text-slate-400">
        <div>Privacy Policy • Terms of Service • Resort Policies</div>
        <div>Powered by Laravel • Designed for Island Living</div>
      </div>
    </div>
  </div>
</footer>
