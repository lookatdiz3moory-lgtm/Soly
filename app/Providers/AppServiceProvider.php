<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Offer;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\FaqPolicy;
use App\Policies\GalleryPolicy;
use App\Policies\OfferPolicy;
use App\Policies\ServicePolicy;
use App\Policies\TestimonialPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    private function registerPolicies(): void
    {
        Gate::policy(Appointment::class,  AppointmentPolicy::class);
        Gate::policy(Doctor::class,       DoctorPolicy::class);
        Gate::policy(Service::class,      ServicePolicy::class);
        Gate::policy(Offer::class,        OfferPolicy::class);
        Gate::policy(Gallery::class,      GalleryPolicy::class);
        Gate::policy(Testimonial::class,  TestimonialPolicy::class);
        Gate::policy(Faq::class,          FaqPolicy::class);
    }

    private function registerGates(): void
    {
        // Superadmin shortcut — granted for every gate check.
        Gate::before(function (User $user, string $ability) {
            if (!$user->is_active) {
                return false;
            }
            return $user->isSuperAdmin() ? true : null;
        });

        // Clinic-wide settings: superadmin + admin only.
        Gate::define('manageSettings', fn (User $user) => $user->isAdmin());

        // User management: superadmin only (enforced via Gate::before).
        Gate::define('manageUsers',    fn (User $user) => false);
    }
}
