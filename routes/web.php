<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PaketWisataController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])
    ->name('landing');

// FAQ chatbot for public visitors
Route::post('/chatbot/message', [ChatbotController::class, 'publicReply'])
    ->middleware('throttle:30,1')
    ->name('chatbot.message');

// Destination Pages
// Requirement 3.1: Routes for two destination pages with SEO URLs
Route::get('/destinasi/{slug}', [DestinationController::class, 'show'])
    ->where('slug', 'the-waterfall|monster-fish')
    ->name('destination.show');

// Package Category Pages
// Requirement 1.1: Routes for package category pages with validation
Route::get('/paket/{category}', [App\Http\Controllers\PackageCategoryController::class, 'show'])
    ->where('category', 'the-waterfall-resto|private-room|fishing-lake')
    ->name('packages.category');

// Package Pages
// Requirement 4.1: Route for package pages with SEO URLs
Route::get('/paket/{slug}', [PackageController::class, 'show'])
    ->name('package.show');

// Static Pages
// Requirement 5.1: Routes for static pages with SEO URLs
Route::get('/wisata-edukasi', [StaticPageController::class, 'education'])
    ->name('education');
Route::get('/wisata-edukasi/sekolah-mitra', [StaticPageController::class, 'schoolPartners'])
    ->name('education.school-partners');
Route::get('/kontak', [StaticPageController::class, 'contact'])
    ->name('contact');

// Sitemap
// Requirement 7.8: Generate sitemap.xml with all public pages
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])
    ->name('sitemap');

// Booking confirmation and e-ticket routes
Route::get('/booking/confirmation/{kode_booking}', [App\Http\Controllers\BookingController::class, 'confirmation'])
    ->middleware('throttle:30,1')
    ->name('booking.confirmation');
Route::get('/booking/e-ticket/{kode_booking}', [App\Http\Controllers\BookingController::class, 'downloadETicket'])
    ->middleware('throttle:10,1')
    ->name('booking.eticket');

if (app()->environment('local')) {
    Route::get('/test/progress-indicator', function () {
        return view('test.progress-indicator-demo');
    })->name('test.progress-indicator');

    Route::get('/test/fishing-booking', function () {
        return view('test.fishing-booking-test');
    })->name('test.fishing-booking');

    Route::get('/test/seo-meta', function () {
        $seoService = app(\App\Services\SEOService::class);
        $testData = [
            'route' => 'destination.show',
            'slug' => 'the-waterfall',
            'name' => 'The Waterfall',
            'description' => 'Nikmati pengalaman luar biasa di The Waterfall, restoran dengan air terjun alami yang menakjubkan.',
            'image' => asset('images/destinations/the-waterfall.jpg'),
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('landing'), 'current' => false],
                ['label' => 'Destinasi', 'url' => null, 'current' => false],
                ['label' => 'The Waterfall', 'url' => null, 'current' => true],
            ],
        ];
        $seoData = $seoService->generateMetadata('destination', $testData);
        return view('test.seo-meta-test', compact('seoData'));
    })->name('test.seo-meta');

    Route::get('/test/midtrans', [App\Http\Controllers\TestMidtransController::class, 'index'])->name('test.midtrans');
    Route::post('/test/midtrans/api', [App\Http\Controllers\TestMidtransController::class, 'testApi'])->name('test.midtrans.api');
}

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.post');
    });
    
    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('chatbot/message', [ChatbotController::class, 'adminReply'])
            ->middleware('throttle:60,1')
            ->name('chatbot.message');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        
        // Booking management
        Route::resource('bookings', BookingController::class)->except(['create', 'store', 'edit']);
        Route::post('bookings/{booking}/update-status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::post('bookings/{booking}/send-email', [BookingController::class, 'sendEmail'])->name('bookings.send-email');
        Route::post('bookings/{booking}/finalize-kiloan', [BookingController::class, 'finalizeKiloan'])->name('bookings.finalize-kiloan');
        Route::get('bookings-export', [BookingController::class, 'export'])->name('bookings.export');
        
        // Paket Wisata management (packages)
        Route::get('packages', [PaketWisataController::class, 'index'])->name('paket-wisata.index');
        Route::get('packages/create', [PaketWisataController::class, 'create'])->name('paket-wisata.create');
        Route::post('packages', [PaketWisataController::class, 'store'])->name('paket-wisata.store');
        Route::get('packages/{paketWisata}', [PaketWisataController::class, 'show'])->name('paket-wisata.show');
        Route::get('packages/{paketWisata}/edit', [PaketWisataController::class, 'edit'])->name('paket-wisata.edit');
        Route::put('packages/{paketWisata}', [PaketWisataController::class, 'update'])->name('paket-wisata.update');
        Route::delete('packages/{paketWisata}', [PaketWisataController::class, 'destroy'])->name('paket-wisata.destroy');
        
        // Customer management
        Route::get('customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{identifier}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        
        // Reports
        Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
        
        // Activity log
        Route::get('activity', [App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('activity');
        
        // Settings
        Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
        Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        
        // Photo Guide
        Route::get('photo-guide', function () {
            return view('admin.photo-guide');
        })->name('photo-guide');
        
        // Notifications
        Route::get('notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread-count', [App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('notifications/{id}/mark-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });
});

// Public API Routes (for booking from landing page)
Route::prefix('api')->middleware(['web', 'throttle:10,1'])->group(function () {
    // Booking store route (guest checkout) - Rate limited to 10 requests per minute
    Route::post('booking/store', [App\Http\Controllers\BookingController::class, 'store'])->name('api.booking.store');
    Route::get('booking/status/{kode_booking}', [App\Http\Controllers\BookingController::class, 'status'])
        ->middleware('throttle:30,1')
        ->name('api.booking.status');
    
    // Fishing booking route - Rate limited to 10 requests per minute
    Route::post('booking/fishing', [App\Http\Controllers\BookingController::class, 'storeFishingBooking'])->name('api.booking.fishing');
    
    // Social proof counter for trust badges - Less restrictive limit
    Route::get('stats/bookings-today', [App\Http\Controllers\BookingController::class, 'bookingsToday'])->name('api.stats.bookings-today');
});

Route::middleware(['web'])->group(function () {
    
    // Midtrans payment notification webhook (CSRF exempt - handled by VerifyCsrfToken middleware)
    Route::post('midtrans/notification', [App\Http\Controllers\BookingController::class, 'notification'])->name('api.midtrans.notification');
    
    // Sync the customer-facing payment callback when the webhook is delayed.
    Route::get('midtrans/check-payment/{orderId}', [App\Http\Controllers\BookingController::class, 'checkPaymentStatus'])
        ->middleware('throttle:10,1')
        ->name('api.midtrans.check-payment');
    
});

