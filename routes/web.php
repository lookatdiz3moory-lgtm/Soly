<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoctorManagementController;
use App\Http\Controllers\Admin\AppointmentManagementController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\Admin\GalleryManagementController;
use App\Http\Controllers\Admin\TestimonialManagementController;
use App\Http\Controllers\Admin\FaqManagementController;
use App\Http\Controllers\Admin\OfferManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingApiController;

/* ═══════════════════════════════════════════════════════════════
   PUBLIC ROUTES
   ═══════════════════════════════════════════════════════════════ */

// ── Locale switcher ─────────────────────────────────────────
// Stores the selected locale in the session and redirects back.
// Validated against config('app.supported_locales') so unknown codes
// silently fall through without changing state.
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, config('app.supported_locales', ['en']), true)) {
        session(['locale' => $locale]);
    }
    return back();
})->where('locale', '[a-z]{2}')->name('locale.switch');

// ── Home ─────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Services ─────────────────────────────────────────────────
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/',        [ServiceController::class, 'index'])->name('index');
    Route::get('/{slug}',  [ServiceController::class, 'show'])->name('show')
         ->where('slug', '[a-z0-9\-]+');
});
// Alias: route('services') resolves to services.index
Route::get('/services', [ServiceController::class, 'index'])->name('services');

// ── Doctors ──────────────────────────────────────────────────
Route::prefix('doctors')->name('doctors.')->group(function () {
    Route::get('/',       [DoctorController::class, 'index'])->name('index');
    Route::get('/{id}',   [DoctorController::class, 'show'])->name('show')
         ->where('id', '[0-9]+');
});
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors');

// ── Booking ──────────────────────────────────────────────────
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/',                         [BookingController::class, 'index'])->name('index');
    Route::get('/confirm/{reference}',      [BookingController::class, 'confirm'])->name('confirm')
         ->where('reference', '[A-Z0-9\-]+');
    Route::get('/cancel/{reference}',       [BookingController::class, 'cancelForm'])->name('cancel.form')
         ->where('reference', '[A-Z0-9\-]+');
    Route::post('/cancel/{reference}',      [BookingController::class, 'cancel'])->name('cancel')
         ->where('reference', '[A-Z0-9\-]+');
});
Route::get('/book',  [BookingController::class, 'index'])->name('booking');

// ── Gallery ──────────────────────────────────────────────────
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

// ── Testimonials ─────────────────────────────────────────────
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials');

// ── Offers ───────────────────────────────────────────────────
Route::prefix('offers')->name('offers.')->group(function () {
    Route::get('/',       [OfferController::class, 'index'])->name('index');
    Route::get('/{slug}', [OfferController::class, 'show'])->name('show');
});
Route::get('/offers', [OfferController::class, 'index'])->name('offers');

// ── FAQ ──────────────────────────────────────────────────────
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// ── Contact ──────────────────────────────────────────────────
Route::prefix('contact')->name('contact.')->group(function () {
    Route::get('/',  [ContactController::class, 'index'])->name('index');
    Route::post('/', [ContactController::class, 'store'])->name('store');
});
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// ── Static / Legal ───────────────────────────────────────────
Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// ── SEO ──────────────────────────────────────────────────────
Route::get('/sitemap.xml',  [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt',   [SitemapController::class, 'robots'])->name('robots');

/* ═══════════════════════════════════════════════════════════════
   JSON API ROUTES (AJAX / Booking Form)
   ═══════════════════════════════════════════════════════════════ */

Route::prefix('api')->name('api.')->middleware(['throttle:60,1'])->group(function () {

    // Available time slots
    Route::get('/available-slots',         [AvailabilityController::class, 'slots'])
         ->name('availability.slots');

    // Doctors by service (for booking form dropdown)
    Route::get('/doctors-by-service/{serviceId}', [AvailabilityController::class, 'doctorsByService'])
         ->name('availability.doctors')
         ->where('serviceId', '[0-9]+');

    // Booking submission
    Route::post('/booking',                [BookingApiController::class, 'store'])
         ->name('booking.store')
         ->middleware('throttle:5,1');

    // Booking slot validation (pre-check before submit)
    Route::post('/booking/validate-slot',  [BookingApiController::class, 'validateSlot'])
         ->name('booking.validate_slot');
});

/* ═══════════════════════════════════════════════════════════════
   ADMIN AUTH ROUTES (No middleware — login page is public)
   ═══════════════════════════════════════════════════════════════ */

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post')
         ->middleware('throttle:5,1');
    Route::post('/logout',[AdminAuthController::class, 'logout'])->name('logout');

    /* ─── Protected Admin Panel ─────────────────────────────── */
    Route::middleware(['auth', 'admin'])->group(function () {

        // Dashboard
        Route::get('/dashboard',        [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats',  [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Appointments
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/',                 [AppointmentManagementController::class, 'index'])->name('index');
            Route::get('/calendar',         [AppointmentManagementController::class, 'calendar'])->name('calendar');
            Route::get('/create',           [AppointmentManagementController::class, 'create'])->name('create');
            Route::post('/create',          [AppointmentManagementController::class, 'store'])->name('store');
            Route::get('/{id}',             [AppointmentManagementController::class, 'show'])->name('show');
            Route::post('/{id}/status',     [AppointmentManagementController::class, 'updateStatus'])->name('status');
            Route::post('/{id}/notes',      [AppointmentManagementController::class, 'updateNotes'])->name('notes');
            Route::post('/{id}/whatsapp',   [AppointmentManagementController::class, 'openWhatsApp'])->name('whatsapp');
        });

        // Doctors
        Route::prefix('doctors')->name('doctors.')->group(function () {
            Route::get('/',             [DoctorManagementController::class, 'index'])->name('index');
            Route::get('/create',       [DoctorManagementController::class, 'create'])->name('create');
            Route::post('/',            [DoctorManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit',    [DoctorManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}',         [DoctorManagementController::class, 'update'])->name('update');
            Route::delete('/{id}',      [DoctorManagementController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/schedule',[DoctorManagementController::class, 'schedule'])->name('schedule');
            Route::put('/{id}/schedule',[DoctorManagementController::class, 'updateSchedule'])->name('schedule.update');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/',            [ServiceManagementController::class, 'index'])->name('index');
            Route::get('/create',      [ServiceManagementController::class, 'create'])->name('create');
            Route::post('/',           [ServiceManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit',   [ServiceManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}',        [ServiceManagementController::class, 'update'])->name('update');
            Route::delete('/{id}',     [ServiceManagementController::class, 'destroy'])->name('destroy');
        });

        // Offers
        Route::prefix('offers')->name('offers.')->group(function () {
            Route::get('/',            [OfferManagementController::class, 'index'])->name('index');
            Route::get('/create',      [OfferManagementController::class, 'create'])->name('create');
            Route::post('/',           [OfferManagementController::class, 'store'])->name('store');
            Route::get('/{id}/edit',   [OfferManagementController::class, 'edit'])->name('edit');
            Route::put('/{id}',        [OfferManagementController::class, 'update'])->name('update');
            Route::delete('/{id}',     [OfferManagementController::class, 'destroy'])->name('destroy');
        });

        // Gallery
        Route::prefix('gallery')->name('gallery.')->group(function () {
            Route::get('/',            [GalleryManagementController::class, 'index'])->name('index');
            Route::post('/upload',     [GalleryManagementController::class, 'upload'])->name('upload');
            Route::delete('/{id}',     [GalleryManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle',[GalleryManagementController::class, 'toggle'])->name('toggle');
        });

        // Testimonials
        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            Route::get('/',            [TestimonialManagementController::class, 'index'])->name('index');
            Route::post('/',           [TestimonialManagementController::class, 'store'])->name('store');
            Route::patch('/{id}/approve', [TestimonialManagementController::class, 'approve'])->name('approve');
            Route::delete('/{id}',     [TestimonialManagementController::class, 'destroy'])->name('destroy');
        });

        // FAQs
        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/',            [FaqManagementController::class, 'index'])->name('index');
            Route::post('/',           [FaqManagementController::class, 'store'])->name('store');
            Route::put('/{id}',        [FaqManagementController::class, 'update'])->name('update');
            Route::delete('/{id}',     [FaqManagementController::class, 'destroy'])->name('destroy');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',            [SettingController::class, 'index'])->name('index');
            Route::put('/',            [SettingController::class, 'update'])->name('update');
            Route::get('/profile',     [SettingController::class, 'profile'])->name('profile');
            Route::put('/profile',     [SettingController::class, 'updateProfile'])->name('profile.update');
            Route::put('/password',    [SettingController::class, 'changePassword'])->name('password');
        });

    }); // end auth+admin middleware
}); // end admin prefix

/* ═══════════════════════════════════════════════════════════════
   LARAVEL AUTH ROUTES (Patient-facing login/register)
   ═══════════════════════════════════════════════════════════════ */

// Uncomment when patient auth is needed:
// require __DIR__.'/auth.php';
