<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\AdminOperationsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactQueryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventEnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagementBookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StaffManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/facilities', [PublicContentController::class, 'facilities'])->name('facilities.index');
Route::get('/restaurant/food', [PublicContentController::class, 'foodMenu'])->name('restaurant.food');
Route::get('/restaurant/bar', [PublicContentController::class, 'barMenu'])->name('restaurant.bar');
Route::get('/about', [PublicContentController::class, 'about'])->name('about');
Route::get('/resort', [PublicContentController::class, 'resort'])->name('resort');
Route::get('/meetings-events', [PublicContentController::class, 'events'])->name('events.index');
Route::post('/meetings-events/enquiry', [EventEnquiryController::class, 'store'])->name('events.enquiry');
Route::get('/reviews', [PublicContentController::class, 'reviews'])->name('reviews.index');
Route::get('/contact', [PublicContentController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactQueryController::class, 'store'])->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::middleware('role:user')->group(function () {
        Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/rooms/{room}/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });

    Route::get('/user/dashboard', [DashboardController::class, 'user'])
        ->middleware('role:user')->name('user.dashboard');
    Route::get('/manager/dashboard', [DashboardController::class, 'manager'])
        ->middleware('role:manager')->name('manager.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin')->name('admin.dashboard');

    Route::middleware('role:admin')->prefix('/admin')->name('admin.')->group(function () {
        Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [AdminRoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
        Route::patch('/rooms/{room}/deactivate', [AdminRoomController::class, 'deactivate'])->name('rooms.deactivate');
        Route::get('/catalog/{type}', [AdminCatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/{type}/create', [AdminCatalogController::class, 'create'])->name('catalog.create');
        Route::post('/catalog/{type}', [AdminCatalogController::class, 'store'])->name('catalog.store');
        Route::get('/catalog/{type}/{id}/edit', [AdminCatalogController::class, 'edit'])->name('catalog.edit');
        Route::put('/catalog/{type}/{id}', [AdminCatalogController::class, 'update'])->name('catalog.update');
        Route::delete('/catalog/{type}/{id}', [AdminCatalogController::class, 'destroy'])->name('catalog.destroy');
        Route::get('/users', [AdminOperationsController::class, 'users'])->name('users.index');
        Route::get('/managers', [AdminOperationsController::class, 'managers'])->name('managers.index');
        Route::post('/managers', [AdminOperationsController::class, 'storeManager'])->name('managers.store');
        Route::put('/managers/{user}', [AdminOperationsController::class, 'updateManager'])->name('managers.update');
        Route::patch('/managers/{user}/status', [AdminOperationsController::class, 'managerStatus'])->name('managers.status');
        Route::get('/administrators', [AdminOperationsController::class, 'administrators'])->name('administrators.index');
        Route::post('/administrators', [AdminOperationsController::class, 'storeAdministrator'])->name('administrators.store');
        Route::put('/administrators/{user}', [AdminOperationsController::class, 'updateAdministrator'])->name('administrators.update');
        Route::patch('/administrators/{user}/status', [AdminOperationsController::class, 'administratorStatus'])->name('administrators.status');
        Route::get('/reviews', [AdminOperationsController::class, 'reviews'])->name('reviews.index');
        Route::patch('/reviews/{review}/status', [AdminOperationsController::class, 'reviewStatus'])->name('reviews.status');
        Route::get('/queries', [AdminOperationsController::class, 'queries'])->name('queries.index');
        Route::patch('/queries/{query}/status', [AdminOperationsController::class, 'queryStatus'])->name('queries.status');
        Route::delete('/queries/{query}', [AdminOperationsController::class, 'deleteQuery'])->name('queries.destroy');
        Route::get('/event-enquiries', [AdminOperationsController::class, 'eventEnquiries'])->name('event-enquiries.index');
        Route::patch('/event-enquiries/{eventEnquiry}/status', [AdminOperationsController::class, 'eventEnquiryStatus'])->name('event-enquiries.status');
        Route::delete('/event-enquiries/{eventEnquiry}', [AdminOperationsController::class, 'deleteEventEnquiry'])->name('event-enquiries.destroy');
        Route::get('/content', [AdminOperationsController::class, 'content'])->name('content.edit');
        Route::put('/content', [AdminOperationsController::class, 'updateContent'])->name('content.update');
    });

    Route::prefix('/management')->name('management.')->middleware('role:admin,manager')->group(function () {
        Route::get('/bookings', [ManagementBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [ManagementBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/approve', [ManagementBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [ManagementBookingController::class, 'reject'])->name('bookings.reject');
        Route::get('/staff', [StaffManagementController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffManagementController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffManagementController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffManagementController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [StaffManagementController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [StaffManagementController::class, 'destroy'])->middleware('role:admin')->name('staff.destroy');
    });
});
