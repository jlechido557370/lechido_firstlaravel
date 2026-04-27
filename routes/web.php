<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookReadController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/browse-ajax', [HomeController::class, 'browseAjax'])->name('browse.ajax');
Route::get('/catalogue', [HomeController::class, 'catalogue'])->name('books.catalogue');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/books/{book}', [HomeController::class, 'show'])->name('books.show');

Route::get('/users/{user}', [ProfileController::class, 'publicProfile'])->name('user.public_profile');
Route::get('/users/{user}/ratings', [ProfileController::class, 'publicRatings'])->name('user.public_ratings');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/books', [AdminDashboardController::class, 'storeBook'])->name('books.store');
    Route::put('/books/{book}', [AdminDashboardController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{book}', [AdminDashboardController::class, 'destroyBook'])->name('books.destroy');
    Route::post('/borrowings/{borrowing}/return', [AdminDashboardController::class, 'returnBook'])->name('borrowings.return');
    Route::post('/reservations/{reservation}/fulfill', [AdminDashboardController::class, 'fulfillReservation'])->name('reservations.fulfill');
    Route::post('/reservations/{reservation}/cancel', [AdminDashboardController::class, 'cancelReservation'])->name('reservations.cancel');
    Route::put('/users/{user}/role', [AdminDashboardController::class, 'updateUserRole'])->name('users.role');
    Route::post('/submissions/{userBook}/approve', [UserBookController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{userBook}/reject', [UserBookController::class, 'reject'])->name('submissions.reject');
});

Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::post('/borrowings/{borrowing}/return', [StaffDashboardController::class, 'returnBook'])->name('borrowings.return');
    Route::post('/reservations/{reservation}/fulfill', [StaffDashboardController::class, 'fulfillReservation'])->name('reservations.fulfill');
    Route::post('/reservations/{reservation}/cancel', [StaffDashboardController::class, 'cancelReservation'])->name('reservations.cancel');
    Route::post('/notify', [StaffDashboardController::class, 'sendNotification'])->name('notify');
    Route::post('/submissions/{userBook}/approve', [UserBookController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{userBook}/reject', [UserBookController::class, 'reject'])->name('submissions.reject');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/payment-history', [UserDashboardController::class, 'paymentHistory'])->name('user.payment_history');
    Route::post('/books/{book}/borrow', [UserDashboardController::class, 'borrowBook'])->name('books.borrow');
    Route::post('/borrowings/{borrowing}/return', [UserDashboardController::class, 'returnBook'])->name('user.borrowings.return');
    Route::post('/books/{book}/reserve', [UserDashboardController::class, 'reserveBook'])->name('books.reserve');
    Route::post('/reservations/{reservation}/cancel', [UserDashboardController::class, 'cancelReservation'])->name('user.reservations.cancel');

    Route::get('/bookmarks', [HomeController::class, 'bookmarks'])->name('books.bookmarks');
    Route::post('/books/{book}/bookmark', [HomeController::class, 'toggleBookmark'])->name('books.bookmark');

    Route::get('/books/{book}/read', [BookReadController::class, 'read'])->name('books.read');

    Route::post('/books/{book}/reviews', [BookReviewController::class, 'store'])->name('books.reviews.store');
    Route::delete('/books/{book}/reviews', [BookReviewController::class, 'destroy'])->name('books.reviews.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('user.profile');
    Route::get('/ratings', [ProfileController::class, 'ratings'])->name('user.ratings');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('user.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('user.avatar.remove');

    Route::get('/payments/initiate/{borrowing}', [PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::post('/payments/process/{borrowing}', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::post('/payments/{payment}/manual-confirm', [PaymentController::class, 'manualConfirm'])->name('payments.manual_confirm');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::get('/notifications/json', [MessageController::class, 'notificationsJson'])->name('notifications.json');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read_all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    Route::post('/follow/user/{user}', [FollowController::class, 'toggleUser'])->name('follow.user');
    Route::post('/follow/author', [FollowController::class, 'toggleAuthor'])->name('follow.author');
    Route::get('/following', [FollowController::class, 'following'])->name('user.following');

    Route::post('/users/{user}/block', [BlockController::class, 'toggle'])->name('block.user');

    Route::get('/publish', [UserBookController::class, 'create'])->name('user.publish');
    Route::post('/publish', [UserBookController::class, 'store'])->name('user.publish.store');
    Route::get('/my-submissions', [UserBookController::class, 'mySubmissions'])->name('user.submissions');

    Route::get('/messages/recent/json', [MessageController::class, 'recentJson'])->name('messages.recent.json');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{user}/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{user}/poll', [MessageController::class, 'poll'])->name('messages.poll');

    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/confirm', [SubscriptionController::class, 'confirmPage'])->name('subscription.confirm');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/subscription/callback', [SubscriptionController::class, 'callback'])->name('subscription.callback');
    Route::get('/subscription/receipt', [SubscriptionController::class, 'receipt'])->name('subscription.receipt');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});
