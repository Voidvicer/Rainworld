<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferry Pass - {{ $ticket->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-cyan-50 min-h-screen flex items-center justify-center p-4">
    <!-- Pass Card -->
    <div class="max-w-md w-full">
        <!-- Action Buttons -->
        <div class="flex gap-3 mb-6 no-print">
            <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Pass
            </button>
            <button onclick="downloadAsImage()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Screenshot
            </button>
            <a href="{{ route('manage.ferry.validate.form') }}" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
        </div>

        <!-- Ferry Pass Card -->
        <div id="ferry-pass" class="bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-blue-200">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 via-cyan-600 to-blue-700 p-6 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm grid place-content-center text-2xl">
                                ⛴️
                            </div>
                            <div>
                                <h1 class="text-xl font-bold">Rainworld Ferry</h1>
                                <p class="text-blue-100 text-sm">Boarding Pass</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-blue-100 uppercase tracking-wider">Pass #</div>
                            <div class="font-mono font-bold text-lg">{{ $ticket->code }}</div>
                        </div>
                    </div>
                    
                    <!-- Passenger Info -->
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 border border-white/30">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-blue-100 uppercase tracking-wider mb-1">Passenger</div>
                                <div class="font-semibold text-lg truncate">{{ $ticket->user->name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-blue-100 uppercase tracking-wider mb-1">Passengers</div>
                                <div class="font-semibold text-lg">{{ $ticket->quantity }} {{ $ticket->quantity == 1 ? 'Person' : 'People' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Wave Pattern -->
                <div class="absolute bottom-0 left-0 right-0 h-4">
                    <svg class="w-full h-full" viewBox="0 0 400 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 10C50 0 100 20 150 10C200 0 250 20 300 10C350 0 400 20 400 10V20H0V10Z" fill="white"/>
                    </svg>
                </div>
            </div>

            <!-- Trip Details -->
            <div class="p-6 space-y-6">
                <!-- Route -->
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-2">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800">{{ $ticket->trip->origin }}</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Departure</div>
                        </div>
                        <div class="flex-1 relative">
                            <div class="h-px bg-gradient-to-r from-blue-300 to-cyan-300"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-white border-2 border-blue-300 rounded-full p-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-slate-800">{{ $ticket->trip->destination }}</div>
                            <div class="text-xs text-slate-500 uppercase tracking-wide">Arrival</div>
                        </div>
                    </div>
                </div>

                <!-- Trip Information Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="text-xs text-slate-500 uppercase tracking-wider mb-2">Date</div>
                        <div class="font-semibold text-slate-800">{{ date('M j, Y', strtotime($ticket->trip->date)) }}</div>
                        <div class="text-sm text-slate-600">{{ date('l', strtotime($ticket->trip->date)) }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="text-xs text-slate-500 uppercase tracking-wider mb-2">Departure Time</div>
                        <div class="font-semibold text-slate-800">{{ date('g:i A', strtotime($ticket->trip->depart_time)) }}</div>
                        <div class="text-sm text-slate-600">Local Time</div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="text-xs text-slate-500 uppercase tracking-wider mb-2">Duration</div>
                        <div class="font-semibold text-slate-800">{{ $ticket->trip->duration }}</div>
                        <div class="text-sm text-slate-600">Estimated</div>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="text-xs text-slate-500 uppercase tracking-wider mb-2">Vessel</div>
                        <div class="font-semibold text-slate-800">{{ $ticket->trip->vessel ?? 'MV Rainworld' }}</div>
                        <div class="text-sm text-slate-600">Ferry</div>
                    </div>
                </div>

                <!-- Pass Details -->
                <div class="border-t border-slate-200 pt-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-500">Issued:</span>
                            <span class="font-medium text-slate-800">{{ $ticket->pass_issued_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500">Amount:</span>
                            <span class="font-medium text-slate-800">${{ number_format($ticket->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="text-center border-t border-slate-200 pt-4">
                    <div class="inline-block bg-slate-800 p-4 rounded-lg">
                        <div class="w-24 h-24 bg-white rounded grid place-content-center">
                            <div class="text-xs text-slate-400">QR Code</div>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-slate-500">
                        Present this pass at boarding
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-xs text-slate-400 border-t border-slate-200 pt-4">
                    <p>Valid for specified trip only • Non-transferable • Rainworld Ferry Services</p>
                    <p class="mt-1">For assistance, contact ferry operations</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadAsImage() {
            // Simple implementation - users can screenshot manually
            alert('To save this pass:\n\n1. Take a screenshot of this page\n2. Or use Print > Save as PDF\n3. Save to your device for offline access');
        }
    </script>
</body>
</html>
