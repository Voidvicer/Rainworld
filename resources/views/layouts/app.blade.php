<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rainworld Picnic Island</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    .gradient-header{background:linear-gradient(90deg,#0f172a,#1e3a8a,#0f766e);} /* dark-indigo to teal */
    .glass{backdrop-filter:blur(10px);background:rgba(255,255,255,.08);}
  </style>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full bg-slate-100/60 dark:bg-slate-900 text-slate-800 dark:text-slate-100 flex flex-col" x-data="{ }" x-init="if(localStorage.getItem('pp_dark')==='1'){document.documentElement.classList.add('dark')}" >
  @include('components.nav')

  <main class="flex-1 w-full">
    <div class="max-w-7xl mx-auto p-4 md:p-6">
      @if (session('success'))<div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('success') }}</div>@endif
      @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-rose-50 text-rose-700 border border-rose-200"><ul class="list-disc list-inside space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      @yield('content')
    </div>
  </main>
  @include('components.footer')

</body>
</html>
