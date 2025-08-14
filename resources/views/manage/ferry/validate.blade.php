@extends('layouts.app')
@section('content')
<div class="mb-8">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/50 grid place-content-center text-xl">✅</div>
          <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Ferry Ticket Validation & Pass Issuance</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Validate tickets and issue QR boarding passes to passengers</p>
          </div>
        </div>
                <a href="{{ route('manage.ferry.dashboard') }}" class="group relative bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700">
          <span class="text-lg">⛴️</span>
          <span>Ferry Dashboard</span>
          <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </a>
        
        @if(config('app.debug'))
        <a href="{{ route('manage.ferry.test.system') }}" target="_blank" class="group relative bg-yellow-500 hover:bg-yellow-600 border border-yellow-600 text-black px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center gap-2">
          <span class="text-sm">🔧</span>
          <span>Test System</span>
        </a>
        @endif
      </div>
    </div>
    
    <!-- Date Filter and Quick Actions -->
    <div class="p-6 bg-slate-50 dark:bg-slate-700/50">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" class="flex items-center gap-4">
          <div>
            <label for="date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Select Date</label>
            <input type="date" id="date" name="date" value="{{ $selectedDate }}" 
                   class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="mt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2">
              <span class="text-lg">🔍</span>
              <span>Load Tickets</span>
            </button>
          </div>
        </form>
        
        <div class="flex gap-2">
          <button id="bulkValidateBtn" disabled class="bg-amber-600 hover:bg-amber-700 disabled:bg-amber-400 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2 disabled:cursor-not-allowed">
            <span class="text-lg">🔍</span>
            <span>Validate Selected</span>
          </button>
          <button id="bulkIssueBtn" disabled class="bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2 disabled:cursor-not-allowed">
            <span class="text-lg">🎫</span>
            <span>Issue Passes</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Success/Error Messages -->
@if(session('pass_issued'))
<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
  <div class="flex items-start gap-3">
    <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/50 grid place-content-center text-green-600 dark:text-green-400 text-sm">✓</div>
    <div>
      <h3 class="font-semibold text-green-800 dark:text-green-400">Pass Issued Successfully!</h3>
      <p class="text-sm text-green-700 dark:text-green-300 mt-1">
        {{ session('pass_issued')['message'] ?? 'Ferry pass has been issued successfully.' }}
      </p>
      @if(session('pass_issued')['ticket'])
      <div class="mt-2 text-xs text-green-600 dark:text-green-400">
        Ticket: {{ session('pass_issued')['ticket']['code'] }} | 
        Passenger: {{ session('pass_issued')['passenger_name'] }} |
        Trip: {{ session('pass_issued')['trip_details']['origin'] }} → {{ session('pass_issued')['trip_details']['destination'] }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

@if($errors->any())
<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
  <div class="flex items-start gap-3">
    <div class="w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 grid place-content-center text-red-600 dark:text-red-400 text-sm">⚠</div>
    <div>
      <h3 class="font-semibold text-red-800 dark:text-red-400">Error</h3>
      @foreach($errors->all() as $error)
      <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ $error }}</p>
      @endforeach
    </div>
  </div>
</div>
@endif

<!-- Quick Stats -->
<div class="grid gap-6 sm:grid-cols-4 mb-8">
  @php
    $totalTickets = $tickets->total();
    $validTickets = $tickets->filter(fn($t) => $t->is_valid)->count();
    $passesIssued = $tickets->filter(fn($t) => $t->pass_issued)->count();
    $pendingPasses = $validTickets - $passesIssued;
  @endphp
  
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Total Tickets</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalTickets }}</div>
        <div class="text-xs text-blue-600 dark:text-blue-400">for {{ date('M j, Y', strtotime($selectedDate)) }}</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/50 grid place-content-center text-2xl">🎫</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Valid Tickets</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $validTickets }}</div>
        <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $totalTickets > 0 ? round(($validTickets / $totalTickets) * 100, 1) : 0 }}% of total</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 grid place-content-center text-2xl">✅</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Passes Issued</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $passesIssued }}</div>
        <div class="text-xs text-purple-600 dark:text-purple-400">with QR codes</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/50 grid place-content-center text-2xl">📱</div>
    </div>
  </div>

  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Pending Passes</div>
        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $pendingPasses }}</div>
        <div class="text-xs text-amber-600 dark:text-amber-400">ready to issue</div>
      </div>
      <div class="w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/50 grid place-content-center text-2xl">⏳</div>
    </div>
  </div>
</div>

<!-- Tickets Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
  <div class="p-6 border-b border-slate-200 dark:border-slate-700">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Ferry Tickets</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400">Select tickets to validate and issue boarding passes</p>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" id="selectAll" class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
        <label for="selectAll" class="text-sm font-medium text-slate-700 dark:text-slate-300">Select All</label>
      </div>
    </div>
  </div>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-600">
      <thead class="bg-slate-50 dark:bg-slate-700">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Select</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Passenger</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ticket Code</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Trip Details</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Quantity</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel Booking</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ferry Pass</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-600">
        @forelse($tickets as $ticket)
          <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors" data-ticket-id="{{ $ticket->id }}">
            <td class="px-6 py-4 whitespace-nowrap">
              <input type="checkbox" class="ticket-checkbox rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500" 
                     value="{{ $ticket->id }}" {{ !$ticket->is_valid ? 'disabled' : '' }}>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 grid place-content-center text-white font-semibold text-xs">
                  {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                </div>
                <div class="ml-3">
                  <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $ticket->user->name }}</div>
                  <div class="text-sm text-slate-500 dark:text-slate-400">{{ $ticket->user->email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-mono text-slate-900 dark:text-slate-100">{{ $ticket->code }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400">${{ number_format($ticket->total_amount, 2) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $ticket->trip->origin }} → {{ $ticket->trip->destination }}</div>
              <div class="text-sm text-slate-500 dark:text-slate-400">
                {{ date('M j, Y', strtotime($ticket->trip->date)) }} at {{ date('g:i A', strtotime($ticket->trip->depart_time)) }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400">
                {{ $ticket->quantity }} {{ $ticket->quantity == 1 ? 'person' : 'people' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->has_hotel_booking ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                {{ $ticket->has_hotel_booking ? '✓ Valid' : '✗ No Booking' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->is_valid ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                {{ $ticket->is_valid ? '✓ Valid' : '✗ Invalid' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              @if($ticket->pass_issued)
                <a href="{{ route('manage.ferry.pass.view', $ticket) }}" target="_blank" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/70 transition-colors">
                  👁️ View Pass
                </a>
              @elseif($ticket->is_valid)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400">
                  Not Issued
                </span>
              @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400">
                  —
                </span>
              @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
              <div class="flex items-center justify-center gap-2">
                @if($ticket->is_valid && !$ticket->pass_issued)
                  <button onclick="issuePass({{ $ticket->id }})" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 text-xs px-3 py-1.5 rounded bg-emerald-100 dark:bg-emerald-900/50 hover:bg-emerald-200 dark:hover:bg-emerald-900/70 transition-colors" title="Issue Pass">
                    🎫 Issue Pass
                  </button>
                @elseif($ticket->pass_issued)
                  <a href="{{ route('manage.ferry.pass.view', $ticket) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-xs px-3 py-1.5 rounded bg-blue-100 dark:bg-blue-900/50 hover:bg-blue-200 dark:hover:bg-blue-900/70 transition-colors" title="View Pass">
                    👁️ View Pass
                  </a>
                @else
                  <span class="text-gray-400 text-xs">—</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
              <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">🎫</div>
                <p class="font-medium">No tickets found for {{ date('M j, Y', strtotime($selectedDate)) }}</p>
                <p class="text-xs">Try selecting a different date</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <div class="p-4 border-t border-slate-200 dark:border-slate-700">
    {{ $tickets->appends(request()->query())->links() }}
  </div>
</div>

<!-- Validation Results Modal -->
<div id="validationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Validation Results</h3>
    </div>
    <div id="validationResults" class="p-6"></div>
    <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex justify-end">
      <button onclick="closeModal('validationModal')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600">
        Close
      </button>
    </div>
  </div>
</div>

<!-- Pass Issue Results Modal -->
<div id="passModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
      <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Pass Issue Results</h3>
    </div>
    <div id="passResults" class="p-6"></div>
    <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex justify-end">
      <button onclick="closeModal('passModal')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600">
        Close
      </button>
    </div>
  </div>
</div>

<script>
// Handle checkbox selection
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');
    const bulkValidateBtn = document.getElementById('bulkValidateBtn');
    const bulkIssueBtn = document.getElementById('bulkIssueBtn');
    
    // Select all functionality
    selectAllBtn.addEventListener('change', function() {
        ticketCheckboxes.forEach(checkbox => {
            if (!checkbox.disabled) {
                checkbox.checked = this.checked;
            }
        });
        updateBulkButtons();
    });
    
    // Individual checkbox change
    ticketCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkButtons();
            
            // Update select all state
            const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
            const enabledBoxes = document.querySelectorAll('.ticket-checkbox:not([disabled])');
            selectAllBtn.checked = checkedBoxes.length === enabledBoxes.length && enabledBoxes.length > 0;
        });
    });
    
    function updateBulkButtons() {
        const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
        const hasSelection = checkedBoxes.length > 0;
        
        bulkValidateBtn.disabled = !hasSelection;
        bulkIssueBtn.disabled = !hasSelection;
    }
    
    // Bulk validate
    bulkValidateBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.ticket-checkbox:checked')).map(cb => cb.value);
        
        fetch('{{ route('manage.ferry.bulk.validate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ticket_ids: selectedIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showValidationResults(data.results);
            } else {
                alert('Error validating tickets');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error validating tickets');
        });
    });
    
    // Bulk issue passes
    bulkIssueBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.ticket-checkbox:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            showNotification('Please select at least one ticket to issue passes', 'warning');
            return;
        }
        
        // Disable button and show loading state
        const originalText = bulkIssueBtn.innerHTML;
        bulkIssueBtn.disabled = true;
        bulkIssueBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span> Issuing Passes...';
        
        fetch('{{ route('manage.ferry.bulk.issue') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ticket_ids: selectedIds
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showPassResults(data);
                showNotification(`Successfully processed ${data.total_tickets} tickets. ${data.issued_count} passes issued, ${data.failed_count} failed.`, 'success');
                
                // Refresh to show updated status after a delay
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                showNotification(data.message || 'Error during bulk pass issuance', 'error');
            }
        })
        .catch(error => {
            console.error('Bulk pass issuance error:', error);
            showNotification('Network error: ' + error.message, 'error');
        })
        .finally(() => {
            // Re-enable button
            bulkIssueBtn.disabled = false;
            bulkIssueBtn.innerHTML = originalText;
        });
    });
});

function showValidationResults(results) {
    const resultsDiv = document.getElementById('validationResults');
    
    let html = '<div class="space-y-4">';
    results.forEach(result => {
        html += `
            <div class="flex items-center justify-between p-4 rounded-lg border ${result.is_valid ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'}">
                <div>
                    <div class="font-medium ${result.is_valid ? 'text-green-900 dark:text-green-100' : 'text-red-900 dark:text-red-100'}">${result.passenger_name}</div>
                    <div class="text-sm ${result.is_valid ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">${result.ticket_code} - ${result.trip_info}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${result.has_hotel_booking ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'}">
                        ${result.has_hotel_booking ? '✓ Hotel' : '✗ No Hotel'}
                    </span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${result.is_valid ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'}">
                        ${result.is_valid ? '✓ Valid' : '✗ Invalid'}
                    </span>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
    document.getElementById('validationModal').classList.remove('hidden');
}

function showPassResults(data) {
    const resultsDiv = document.getElementById('passResults');
    
    let html = `
        <div class="mb-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <div class="text-blue-900 dark:text-blue-100 font-medium">Summary</div>
            <div class="text-blue-700 dark:text-blue-300 text-sm">
                ${data.issued_count} passes issued successfully, ${data.failed_count} failed
            </div>
        </div>
        <div class="space-y-4">
    `;
    
    if (data.issued.length > 0) {
        html += '<div class="mb-4"><h4 class="font-medium text-green-900 dark:text-green-100 mb-2">✓ Successfully Issued</h4>';
        data.issued.forEach(item => {
            html += `
                <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <div class="text-green-900 dark:text-green-100 font-medium">${item.passenger_name}</div>
                    <div class="text-green-700 dark:text-green-300 text-sm">${item.ticket_code}</div>
                </div>
            `;
        });
        html += '</div>';
    }
    
    if (data.failed.length > 0) {
        html += '<div><h4 class="font-medium text-red-900 dark:text-red-100 mb-2">✗ Failed to Issue</h4>';
        data.failed.forEach(item => {
            html += `
                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <div class="text-red-900 dark:text-red-100 font-medium">${item.passenger_name}</div>
                    <div class="text-red-700 dark:text-red-300 text-sm">${item.ticket_code} - ${item.reason}</div>
                </div>
            `;
        });
        html += '</div>';
    }
    
    html += '</div>';
    
    resultsDiv.innerHTML = html;
    document.getElementById('passModal').classList.remove('hidden');
}

function issuePass(ticketId) {
    // Validate input
    if (!ticketId || isNaN(ticketId)) {
        showNotification('Invalid ticket ID provided', 'error');
        return;
    }

    // Disable the button to prevent double-clicks
    const button = document.querySelector(`button[onclick="issuePass(${ticketId})"]`);
    const originalText = button ? button.innerHTML : '';
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span> Issuing...';
    }

    // Make the API call
    fetch('{{ route('manage.ferry.issue.pass') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            ticket_id: parseInt(ticketId)
        })
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || `HTTP ${response.status}: ${response.statusText}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Optional: Show pass details in a modal
            if (data.data && data.data.pass_url) {
                showPassIssuedModal(data.data);
            }
            
            // Reload the page after a short delay to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to issue ferry pass', 'error');
        }
    })
    .catch(error => {
        console.error('Ferry pass issuance error:', error);
        showNotification('Network error: ' + error.message, 'error');
    })
    .finally(() => {
        // Re-enable the button
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="text-lg">
                ${type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️'}
            </div>
            <div class="font-medium">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-xl leading-none">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

function showPassIssuedModal(passData) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4 relative">
            <button onclick="this.closest('div[class*=\"fixed\"]').remove()" class="absolute top-4 right-4 text-slate-500 hover:text-slate-700 text-xl">&times;</button>
            
            <div class="text-center mb-4">
                <div class="text-6xl mb-2">🎫</div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Ferry Pass Issued!</h3>
            </div>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Passenger:</span>
                    <span class="font-medium text-slate-900 dark:text-slate-100">${passData.passenger_name}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Ticket:</span>
                    <span class="font-medium text-slate-900 dark:text-slate-100">${passData.ticket_code}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Route:</span>
                    <span class="font-medium text-slate-900 dark:text-slate-100">${passData.route}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Date & Time:</span>
                    <span class="font-medium text-slate-900 dark:text-slate-100">${passData.trip_date} ${passData.trip_time}</span>
                </div>
            </div>
            
            <div class="mt-6 flex gap-3">
                <a href="${passData.pass_url}" target="_blank" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-center text-sm font-medium transition-colors">
                    View Pass
                </a>
                <button onclick="this.closest('div[class*=\"fixed\"]').remove()" class="flex-1 bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
@endsection
