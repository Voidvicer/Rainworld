<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\FerryTripController;
use App\Http\Controllers\FerryTicketController;
use App\Http\Controllers\TicketValidationController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\HotelManagementController;
use App\Http\Controllers\Admin\FerryManagementController;

Route::get('/', [DashboardController::class, 'home'])->name('home');

// Debug route for testing authentication
Route::get('/debug-auth', function() {
    return view('debug-auth');
})->name('debug.auth');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'home'])
    ->middleware(['auth'])
    ->name('dashboard');

// Debug route to test roles
Route::middleware(['auth'])->get('/debug-roles', function(Request $request) {
    $user = $request->user();
    
    return response()->json([
        'user' => $user->email,
        'roles' => $user->roles->pluck('name'),
        'has_admin' => $user->hasRole('admin'),
        'has_any_role' => $user->hasAnyRole(['admin', 'hotel_manager', 'ferry_staff']),
        'all_roles' => \Spatie\Permission\Models\Role::all()->pluck('name')
    ]);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
    Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms/{room}/book/prepare', [HotelBookingController::class, 'prepare'])->name('bookings.prepare');
    Route::get('/rooms/{room}/book/checkout', [HotelBookingController::class, 'checkout'])->name('bookings.checkout');
    Route::post('/rooms/{room}/book', [HotelBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [HotelBookingController::class, 'index'])->name('bookings.index');
    Route::delete('/bookings/{booking}', [HotelBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/ferry/trips', [FerryTripController::class, 'index'])->name('ferry.trips.index');
    Route::get('/ferry/tickets/bulk/prepare', [FerryTicketController::class, 'bulkPrepare'])->name('ferry.tickets.bulk.prepare');
    Route::post('/ferry/tickets/bulk/prepare', [FerryTicketController::class, 'bulkPrepare']);
    Route::post('/ferry/tickets/bulk/store', [FerryTicketController::class, 'bulkStore'])->name('ferry.tickets.bulk.store');
    Route::get('/ferry/trips/{trip}/tickets/prepare', [FerryTicketController::class, 'prepare'])->name('ferry.tickets.prepare');
    Route::post('/ferry/trips/{trip}/tickets/prepare', [FerryTicketController::class, 'prepare']);
    Route::get('/ferry/trips/{trip}/tickets/checkout', [FerryTicketController::class, 'checkout'])->name('ferry.tickets.checkout');
    Route::post('/ferry/trips/{trip}/tickets', [FerryTicketController::class, 'store'])->name('ferry.tickets.store');
    Route::get('/ferry/tickets', [FerryTicketController::class, 'index'])->name('ferry.tickets.index');
    Route::delete('/ferry/tickets/{ferry_ticket}', [FerryTicketController::class, 'cancel'])->name('ferry.tickets.cancel');


});

Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':hotel_manager|admin'])->group(function () {
    Route::resource('manage/hotels', HotelController::class)->except(['index', 'show']);
    Route::resource('manage/rooms', RoomController::class)->except(['show']);
    Route::get('manage/hotel-bookings', [HotelBookingController::class, 'manage'])->name('manage.bookings');
    Route::patch('manage/hotel-bookings/{booking}/status', [HotelBookingController::class, 'updateStatus'])->name('manage.bookings.status');
    
    // Hotel Management Dashboard for Hotel Staff
    Route::get('manage/hotel/dashboard', [HotelManagementController::class, 'dashboard'])->name('manage.hotel.dashboard');
    Route::get('manage/hotel/availability', [HotelManagementController::class, 'roomAvailability'])->name('manage.hotel.availability');
    Route::get('manage/hotel/reports', [HotelManagementController::class, 'bookingReports'])->name('manage.hotel.reports');
    Route::get('manage/hotel/reports-advanced', [HotelManagementController::class, 'bookingReports'])->name('manage.hotel.reports.advanced');
    
    // Hotel Promotion Management
    Route::get('manage/hotel/promotions', [HotelManagementController::class, 'promotionManagement'])->name('manage.hotel.promotions');
    Route::post('manage/hotel/promotions', [HotelManagementController::class, 'storePromotion'])->name('manage.hotel.promotions.store');
    Route::put('manage/hotel/promotions/{id}', [HotelManagementController::class, 'updatePromotion'])->name('manage.hotel.promotions.update');
    Route::patch('manage/hotel/promotions/{id}/deactivate', [HotelManagementController::class, 'deactivatePromotion'])->name('manage.hotel.promotions.deactivate');
    Route::delete('manage/hotel/promotions/{id}', [HotelManagementController::class, 'deletePromotion'])->name('manage.hotel.promotions.delete');
});

Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':ferry_staff|admin'])->group(function () {
    Route::get('manage/ferry-trips', [FerryTripController::class, 'manageIndex'])->name('manage.ferry-trips.index');
    Route::get('manage/ferry-trips/create', [FerryTripController::class, 'create'])->name('manage.ferry-trips.create');
    Route::post('manage/ferry-trips', [FerryTripController::class, 'store'])->name('manage.ferry-trips.store');
    Route::get('manage/ferry-trips/{ferry_trip}/edit', [FerryTripController::class, 'edit'])->name('manage.ferry-trips.edit');
    Route::put('manage/ferry-trips/{ferry_trip}', [FerryTripController::class, 'update'])->name('manage.ferry-trips.update');
    Route::delete('manage/ferry-trips/{ferry_trip}', [FerryTripController::class, 'destroy'])->name('manage.ferry-trips.destroy');
    Route::get('manage/ferry/validate', [TicketValidationController::class, 'ferryForm'])->name('manage.ferry.validate.form');
    Route::post('manage/ferry/validate', [TicketValidationController::class, 'ferryCheck'])->name('manage.ferry.validate.check');
    Route::post('manage/ferry/bulk-validate', [TicketValidationController::class, 'bulkValidate'])->name('manage.ferry.bulk.validate');
    Route::post('manage/ferry/issue-pass', [TicketValidationController::class, 'issuePass'])->name('manage.ferry.issue.pass');
    Route::post('manage/ferry/bulk-issue', [TicketValidationController::class, 'bulkIssuePass'])->name('manage.ferry.bulk.issue');
    Route::get('manage/ferry/pass/{ticket}', [TicketValidationController::class, 'viewPass'])->name('manage.ferry.pass.view');
    Route::get('manage/ferry/test-system', [TicketValidationController::class, 'testPassSystem'])->name('manage.ferry.test.system');
    Route::get('manage/ferry/reports', [FerryManagementController::class, 'tripReports'])->name('manage.ferry.reports');
    Route::get('manage/ferry/export-trips', [FerryManagementController::class, 'exportTrips'])->name('manage.ferry.export.trips');
    Route::get('manage/ferry/export-revenue', [FerryManagementController::class, 'exportRevenue'])->name('manage.ferry.export.revenue');
    Route::patch('manage/ferry/tickets/{ferryTicket}/status', [FerryTicketController::class, 'updateStatus'])->name('manage.ferry.tickets.status');
    
    // Ferry Management Dashboard for Ferry Staff
    Route::get('manage/ferry/dashboard', [FerryManagementController::class, 'dashboard'])->name('manage.ferry.dashboard');
    Route::get('manage/ferry/reports', [FerryManagementController::class, 'reports'])->name('manage.ferry.reports');
    Route::get('manage/ferry/passengers-advanced/{trip?}', [FerryManagementController::class, 'passengerLists'])->name('manage.ferry.passengers.advanced');
    Route::patch('manage/ferry/trips/{trip}/status', [FerryManagementController::class, 'updateTripStatus'])->name('manage.ferry.trips.status');
    Route::get('manage/ferry/trips/{trip}', [FerryManagementController::class, 'getTripData'])->name('manage.ferry.trips.show');
    Route::post('manage/ferry/trips', [FerryManagementController::class, 'storeTrip'])->name('manage.ferry.trips.store');
    Route::put('manage/ferry/trips/{trip}', [FerryManagementController::class, 'updateTrip'])->name('manage.ferry.trips.update');
});


Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/ads', [AdminController::class, 'ads'])->name('admin.ads');
    Route::post('/admin/ads', [AdminController::class, 'storeAd'])->name('admin.ads.store');
    Route::put('/admin/ads/{id}', [AdminController::class, 'updateAd'])->name('admin.ads.update');
    Route::patch('/admin/ads/{id}/deactivate', [AdminController::class, 'deactivateAd'])->name('admin.ads.deactivate');
    Route::delete('/admin/ads/{id}', [AdminController::class, 'deleteAd'])->name('admin.ads.delete');
    Route::get('/admin/map', [AdminController::class, 'map'])->name('admin.map');
    Route::post('/admin/map', [AdminController::class, 'storeLocation'])->name('admin.map.store');
    
    // User Management Routes
    Route::resource('/admin/users', UserManagementController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create', 
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy'
    ]);
    Route::patch('/admin/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    
    // Enhanced Hotel Management
    Route::get('/admin/hotels/dashboard', [HotelManagementController::class, 'dashboard'])->name('admin.hotels.dashboard');
    Route::get('/admin/hotels/availability', [HotelManagementController::class, 'roomAvailability'])->name('admin.hotels.availability');
    Route::get('/admin/hotels/reports', [HotelManagementController::class, 'bookingReports'])->name('admin.hotels.reports');
    Route::get('/admin/hotels/promotions', [HotelManagementController::class, 'promotionManagement'])->name('admin.hotels.promotions');
    Route::post('/admin/hotels/promotions', [HotelManagementController::class, 'storePromotion'])->name('admin.hotels.promotions.store');
    
    // Enhanced Ferry Management  
    Route::get('/admin/ferry/dashboard', [FerryManagementController::class, 'dashboard'])->name('admin.ferry.dashboard');
    Route::get('/admin/ferry/schedule', [FerryManagementController::class, 'schedule'])->name('admin.ferry.schedule');
    Route::get('/admin/ferry/passengers/{trip?}', [FerryManagementController::class, 'passengerLists'])->name('admin.ferry.passengers');
    Route::post('/admin/ferry/pass/issue', [FerryManagementController::class, 'issueFerryPass'])->name('admin.ferry.issue-pass');
    Route::post('/admin/ferry/pass/{booking}', [FerryManagementController::class, 'issueFerryPass'])->name('admin.ferry.pass.issue');
    Route::get('/admin/ferry/reports', [FerryManagementController::class, 'tripReports'])->name('admin.ferry.reports');
    Route::get('/admin/ferry/export-trips', [FerryManagementController::class, 'exportTrips'])->name('admin.ferry.export.trips');
    Route::get('/admin/ferry/export-revenue', [FerryManagementController::class, 'exportRevenue'])->name('admin.ferry.export.revenue');
    Route::patch('/admin/ferry/trips/{trip}/status', [FerryManagementController::class, 'updateTripStatus'])->name('admin.ferry.trips.status');
    Route::get('/admin/ferry/trips/{trip}', [FerryManagementController::class, 'getTripData'])->name('admin.ferry.trips.show');
    Route::post('/admin/ferry/trips', [FerryManagementController::class, 'storeTrip'])->name('admin.ferry.trips.store');
    Route::put('/admin/ferry/trips/{trip}', [FerryManagementController::class, 'updateTrip'])->name('admin.ferry.trips.update');
    Route::post('/admin/ferry/validate-advanced', [FerryManagementController::class, 'validateTicketAdvanced'])->name('admin.ferry.validate-advanced');
});

require __DIR__.'/auth.php';
