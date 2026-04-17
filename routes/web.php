<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookReadController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Public routes ─────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/catalogue', [HomeController::class, 'catalogue'])->name('books.catalogue');
Route::get('/books/{book}', [HomeController::class, 'show'])->name('books.show');

// Public user profiles
Route::get('/users/{user}', [ProfileController::class, 'publicProfile'])->name('user.public_profile');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Admin routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/books', [AdminDashboardController::class, 'storeBook'])->name('books.store');
    Route::put('/books/{book}', [AdminDashboardController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [AdminDashboardController::class, 'destroyBook'])->name('books.destroy');
    Route::post('/borrowings/{borrowing}/return', [AdminDashboardController::class, 'returnBook'])->name('borrowings.return');
    Route::post('/reservations/{reservation}/fulfill', [AdminDashboardController::class, 'fulfillReservation'])->name('reservations.fulfill');
    Route::post('/reservations/{reservation}/cancel', [AdminDashboardController::class, 'cancelReservation'])->name('reservations.cancel');
});

// ── User routes ───────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/books/{book}/borrow', [UserDashboardController::class, 'borrowBook'])->name('books.borrow');
    Route::post('/borrowings/{borrowing}/return', [UserDashboardController::class, 'returnBook'])->name('user.borrowings.return');
    Route::post('/books/{book}/reserve', [UserDashboardController::class, 'reserveBook'])->name('books.reserve');
    Route::post('/reservations/{reservation}/cancel', [UserDashboardController::class, 'cancelReservation'])->name('user.reservations.cancel');

    // Bookmarks
    Route::get('/bookmarks', [HomeController::class, 'bookmarks'])->name('books.bookmarks');
    Route::post('/books/{book}/bookmark', [HomeController::class, 'toggleBookmark'])->name('books.bookmark');

    // Read a borrowed book
    Route::get('/books/{book}/read', [BookReadController::class, 'read'])->name('books.read');

    // Reviews
    Route::post('/books/{book}/reviews', [BookReviewController::class, 'store'])->name('books.reviews.store');
    Route::delete('/books/{book}/reviews', [BookReviewController::class, 'destroy'])->name('books.reviews.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('user.avatar.remove');
});