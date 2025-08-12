<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\FerryTripController;
use App\Http\Controllers\FerryTicketController;
use App\Http\Controllers\TicketValidationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityScheduleController;
use App\Http\Controllers\ThemeParkTicketController;
use App\Http\Controllers\ActivityBookingController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', [DashboardController::class, 'home'])->name('home');
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'home'])
    ->middleware(['auth'])
    ->name('dashboard');


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

    Route::get('/park/tickets', [ThemeParkTicketController::class, 'index'])->name('park.tickets.index');
    Route::post('/park/tickets/prepare', [ThemeParkTicketController::class, 'prepare'])->name('park.tickets.prepare');
    Route::get('/park/tickets/checkout', [ThemeParkTicketController::class, 'checkout'])->name('park.tickets.checkout');
    Route::post('/park/tickets', [ThemeParkTicketController::class, 'store'])->name('park.tickets.store');

    Route::get('/activities', [ActivityController::class, 'listPublic'])->name('activities.public');
    Route::get('/activities/{schedule}/book', [ActivityBookingController::class, 'create'])->name('activity.book.create');
    Route::post('/activities/{schedule}/book/prepare', [ActivityBookingController::class, 'prepare'])->name('activity.book.prepare');
    Route::get('/activities/{schedule}/book/checkout', [ActivityBookingController::class, 'checkout'])->name('activity.book.checkout');
    Route::post('/activities/{schedule}/book', [ActivityBookingController::class, 'store'])->name('activity.book.store');
    Route::get('/activity-bookings', [ActivityBookingController::class, 'index'])->name('activity.bookings.index');
});

Route::middleware(['auth', 'role:hotel_manager|admin'])->group(function () {
    Route::resource('manage/hotels', HotelController::class)->except(['index', 'show']);
    Route::resource('manage/rooms', RoomController::class)->except(['show']);
    Route::get('manage/hotel-bookings', [HotelBookingController::class, 'manage'])->name('manage.bookings');
    Route::patch('manage/hotel-bookings/{booking}/status', [HotelBookingController::class, 'updateStatus'])->name('manage.bookings.status');
});

Route::middleware(['auth', 'role:ferry_staff|admin'])->group(function () {
    Route::resource('manage/ferry-trips', FerryTripController::class)->except(['index', 'show']);
    Route::get('manage/ferry/validate', [TicketValidationController::class, 'ferryForm'])->name('manage.ferry.validate.form');
    Route::post('manage/ferry/validate', [TicketValidationController::class, 'ferryCheck'])->name('manage.ferry.validate.check');
    Route::get('manage/ferry/reports', [FerryTicketController::class, 'reports'])->name('manage.ferry.reports');
});

Route::middleware(['auth', 'role:theme_staff|admin'])->group(function () {
    Route::resource('manage/activities', ActivityController::class)->except(['show']);
    Route::resource('manage/activity-schedules', ActivityScheduleController::class)->except(['show']);
    Route::get('manage/park/ticket-sales', [ThemeParkTicketController::class, 'reports'])->name('manage.park.reports');
    Route::get('manage/park/validate', [TicketValidationController::class, 'parkForm'])->name('manage.park.validate.form');
    Route::post('manage/park/validate', [TicketValidationController::class, 'parkCheck'])->name('manage.park.validate.check');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [Admin\AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/reports', [Admin\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/ads', [Admin\AdminController::class, 'ads'])->name('admin.ads');
    Route::post('/admin/ads', [Admin\AdminController::class, 'storeAd'])->name('admin.ads.store');
    Route::get('/admin/map', [Admin\AdminController::class, 'map'])->name('admin.map');
    Route::post('/admin/map', [Admin\AdminController::class, 'storeLocation'])->name('admin.map.store');
});

require __DIR__.'/auth.php';
