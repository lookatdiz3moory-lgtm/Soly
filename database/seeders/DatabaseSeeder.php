<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ServiceSeeder::class,
            DoctorSeeder::class,
            TestimonialSeeder::class,
            FaqSeeder::class,
            SettingSeeder::class,
        ]);
    }
}

/* ════════════════════════════════════════════════════════════
   ADMIN USER SEEDER
   ════════════════════════════════════════════════════════════ */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@solyclinic.com'],
            [
                'name'              => 'Dr. Selim',
                'email'             => 'admin@solyclinic.com',
                'password'          => Hash::make('Admin@2024'),
                'role'              => 'superadmin',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );
        $this->command->info('✅ Admin user created: admin@solyclinic.com / Admin@2024');
        $this->command->warn('   ⚠️  Change this password immediately after first login!');
    }
}

/* ════════════════════════════════════════════════════════════
   SERVICE SEEDER
   ════════════════════════════════════════════════════════════ */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'              => 'Teeth Cleaning',
                'slug'              => 'teeth-cleaning',
                'icon'              => '🦷',
                'category'          => 'preventive',
                'short_description' => 'Professional scaling and polishing for a healthy, fresh mouth. Removes tartar and plaque buildup effectively.',
                'price_from'        => 150.00,
                'duration_minutes'  => 45,
                'is_featured'       => false,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Scaling & Polishing',
                'slug'              => 'scaling-polishing',
                'icon'              => '✨',
                'category'          => 'preventive',
                'short_description' => 'Deep cleaning that removes stubborn tartar below the gum line, followed by a high-shine polish.',
                'price_from'        => 200.00,
                'duration_minutes'  => 60,
                'is_featured'       => false,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'Veneers',
                'slug'              => 'veneers',
                'icon'              => '✨',
                'category'          => 'cosmetic',
                'short_description' => 'Ultra-thin porcelain shells that transform your smile. Natural-looking, stain-resistant, long-lasting.',
                'price_from'        => 2500.00,
                'duration_minutes'  => 90,
                'is_featured'       => true,
                'sort_order'        => 3,
            ],
            [
                'name'              => 'Zircon Crowns',
                'slug'              => 'zircon-crowns',
                'icon'              => '💎',
                'category'          => 'cosmetic',
                'short_description' => 'Metal-free zirconia crowns with exceptional strength and a completely natural appearance.',
                'price_from'        => 1800.00,
                'duration_minutes'  => 90,
                'is_featured'       => true,
                'sort_order'        => 4,
            ],
            [
                'name'              => 'Hollywood Smile',
                'slug'              => 'hollywood-smile',
                'icon'              => '😁',
                'category'          => 'cosmetic',
                'short_description' => 'Complete smile transformation combining veneers, whitening, and contouring for a camera-ready result.',
                'price_from'        => 8000.00,
                'duration_minutes'  => 120,
                'is_featured'       => true,
                'sort_order'        => 5,
            ],
            [
                'name'              => 'Dental Implants',
                'slug'              => 'dental-implants',
                'icon'              => '🏆',
                'category'          => 'implants',
                'short_description' => 'Permanent titanium implants that look, feel, and function exactly like your natural teeth.',
                'price_from'        => 6000.00,
                'duration_minutes'  => 60,
                'is_featured'       => true,
                'sort_order'        => 6,
            ],
            [
                'name'              => 'Orthodontics',
                'slug'              => 'orthodontics',
                'icon'              => '📐',
                'category'          => 'orthodontics',
                'short_description' => 'Traditional braces and clear aligners for a perfectly aligned, confident smile at any age.',
                'price_from'        => 5000.00,
                'duration_minutes'  => 60,
                'is_featured'       => true,
                'sort_order'        => 7,
            ],
            [
                'name'              => 'Root Canal Treatment',
                'slug'              => 'root-canal',
                'icon'              => '🔬',
                'category'          => 'restorative',
                'short_description' => 'Pain-free root canal therapy to save your natural tooth and eliminate infection permanently.',
                'price_from'        => 800.00,
                'duration_minutes'  => 90,
                'is_featured'       => false,
                'sort_order'        => 8,
            ],
            [
                'name'              => 'Teeth Whitening',
                'slug'              => 'teeth-whitening',
                'icon'              => '⭐',
                'category'          => 'cosmetic',
                'short_description' => 'Professional in-office whitening — up to 8 shades brighter in a single 60-minute session.',
                'price_from'        => 500.00,
                'duration_minutes'  => 60,
                'is_featured'       => true,
                'sort_order'        => 9,
            ],
            [
                'name'              => 'Facial Skin Care',
                'slug'              => 'facial-skincare',
                'icon'              => '💆',
                'category'          => 'skincare',
                'short_description' => 'Advanced facial treatments to complement your smile — HydraFacial, PRP, and rejuvenation therapy.',
                'price_from'        => 400.00,
                'duration_minutes'  => 60,
                'is_featured'       => false,
                'sort_order'        => 10,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['slug' => $service['slug']],
                array_merge($service, [
                    'is_bookable' => true,
                    'is_active'   => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])
            );
        }
        $this->command->info('✅ Services seeded: ' . count($services) . ' services');
    }
}

/* ════════════════════════════════════════════════════════════
   DOCTOR SEEDER
   ════════════════════════════════════════════════════════════ */
class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSchedule = [
            'sunday'    => ['open' => '09:00', 'close' => '21:00', 'active' => true],
            'monday'    => ['open' => '09:00', 'close' => '21:00', 'active' => true],
            'tuesday'   => ['open' => '09:00', 'close' => '21:00', 'active' => true],
            'wednesday' => ['open' => '09:00', 'close' => '21:00', 'active' => true],
            'thursday'  => ['open' => '09:00', 'close' => '21:00', 'active' => true],
            'friday'    => ['open' => '09:00', 'close' => '21:00', 'active' => false],
            'saturday'  => ['open' => '10:00', 'close' => '18:00', 'active' => true],
        ];

        $doctorId = DB::table('doctors')->insertGetId([
            'name'          => 'Dr. Salim',
            'title'         => 'BDS, Military Medical Corps',
            'specialty'     => 'General & Cosmetic Dentistry',
            'bio'           => 'Founder of Soly Clinic and trusted by hundreds of patients across Zahraa Maadi. Known for honest diagnoses, precision technique, and fair pricing. Proud graduate of military medical service. Dr. Salim believes every patient deserves world-class dental care without the world-class price tag.',
            'phone'         => config('clinic.phone', '+20 100 582 6642'),
            'experience'    => '10+ Years',
            'specialties'   => json_encode(['Implants', 'Hollywood Smile', 'Veneers', 'Cosmetics', 'Root Canal']),
            'schedule'      => json_encode($defaultSchedule),
            'slot_duration' => 30,
            'is_active'     => true,
            'sort_order'    => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Sync all services to this doctor
        $serviceIds = DB::table('services')->pluck('id');
        foreach ($serviceIds as $sid) {
            DB::table('doctor_service')->insertOrIgnore([
                'doctor_id'  => $doctorId,
                'service_id' => $sid,
            ]);
        }

        $this->command->info("✅ Doctor seeded: Dr. Salim (ID: {$doctorId})");
    }
}

/* ════════════════════════════════════════════════════════════
   TESTIMONIAL SEEDER
   ════════════════════════════════════════════════════════════ */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Nadia Hassan',   'rating' => 5, 'service' => 'Hollywood Smile',   'review' => 'I was embarrassed to smile for years. Dr. Salim understood exactly what I wanted and the result exceeded every expectation. I cannot stop smiling now!',        'source' => 'google',   'is_featured' => true],
            ['name' => 'Ahmed Mostafa',  'rating' => 5, 'service' => 'Dental Implants',   'review' => 'Had a missing tooth for 3 years. The implant feels completely natural — I actually forget it\'s there. Professional, painless, and fairly priced.',              'source' => 'google',   'is_featured' => true],
            ['name' => 'Sara El-Sayed',  'rating' => 5, 'service' => 'Veneers',           'review' => 'Soly Clinic is the first place where the doctor told me exactly what I needed — and nothing more. The veneers are absolutely stunning.',                          'source' => 'whatsapp', 'is_featured' => true],
            ['name' => 'Khaled Ibrahim', 'rating' => 5, 'service' => 'Teeth Whitening',   'review' => 'Fast, effective, and the results lasted months. I whitened at two other clinics before — nothing compares to the results here.',                                  'source' => 'google',   'is_featured' => true],
            ['name' => 'Mariam Adel',    'rating' => 5, 'service' => 'Root Canal',        'review' => 'Was terrified of root canal. Dr. Salim walked me through every step and I felt zero pain during or after. Changed my whole view of dental treatment.',             'source' => 'google',   'is_featured' => false],
            ['name' => 'Omar Fathy',     'rating' => 5, 'service' => 'Zircon Crowns',     'review' => 'The crowns look completely natural. Cannot tell the difference from real teeth — and I\'ve gotten so many compliments. Dr. Salim is incredibly skilled.',          'source' => 'facebook', 'is_featured' => false],
        ];

        foreach ($testimonials as $i => $t) {
            DB::table('testimonials')->insert(array_merge($t, [
                'is_approved' => true,
                'is_active'   => true,
                'sort_order'  => $i + 1,
                'created_at'  => now()->subDays(rand(7, 90)),
                'updated_at'  => now(),
            ]));
        }
        $this->command->info('✅ Testimonials seeded: ' . count($testimonials));
    }
}

/* ════════════════════════════════════════════════════════════
   FAQ SEEDER
   ════════════════════════════════════════════════════════════ */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['category' => 'general', 'is_featured' => true,  'question' => 'Is teeth whitening safe?',          'answer' => 'Yes, professional teeth whitening at our clinic uses clinically tested, enamel-safe whitening agents under controlled conditions. It is completely safe when performed professionally.'],
            ['category' => 'general', 'is_featured' => true,  'question' => 'How long does a dental implant take?', 'answer' => 'The implant surgery takes 1–2 hours. Full completion including healing and crown placement typically takes 3–6 months, depending on your bone density.'],
            ['category' => 'booking', 'is_featured' => true,  'question' => 'How do I book an appointment?',     'answer' => 'You can book online through our website 24/7, via WhatsApp, by phone, or by visiting us in person. Online booking is the fastest option.'],
            ['category' => 'booking', 'is_featured' => true,  'question' => 'Can I cancel or reschedule?',       'answer' => 'Yes, you can cancel or reschedule up to 24 hours before your appointment at no charge. Simply contact us via WhatsApp or phone.'],
            ['category' => 'general', 'is_featured' => true,  'question' => 'Do you offer payment plans?',       'answer' => 'Yes, we offer flexible payment arrangements for major procedures. Please discuss this with our team during your initial consultation.'],
            ['category' => 'general', 'is_featured' => false, 'question' => 'Is the clinic safe for children?',  'answer' => 'Absolutely. We treat all ages with equal patience and care. Our child-friendly approach ensures a positive experience for young patients.'],
            ['category' => 'general', 'is_featured' => false, 'question' => 'Do you use painless anaesthesia?',  'answer' => 'Yes. We use the latest local anaesthesia techniques to ensure every procedure is completely comfortable. Pain-free dentistry is our standard.'],
            ['category' => 'general', 'is_featured' => false, 'question' => 'How often should I visit?',         'answer' => 'We recommend a checkup and cleaning every 6 months to maintain optimal dental health and catch any issues early.'],
        ];

        foreach ($faqs as $i => $faq) {
            DB::table('faqs')->insert(array_merge($faq, [
                'is_active'  => true,
                'sort_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('✅ FAQs seeded: ' . count($faqs));
    }
}

/* ════════════════════════════════════════════════════════════
   SETTINGS SEEDER
   ════════════════════════════════════════════════════════════ */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'clinic_name',        'value' => 'Soly Clinic',                       'group' => 'general',  'label' => 'Clinic Name',       'is_public' => true],
            ['key' => 'clinic_tagline',      'value' => 'Premium Dental Care in Zahraa Maadi',  'group' => 'general',  'label' => 'Tagline',            'is_public' => true],
            ['key' => 'clinic_phone',        'value' => '+20 100 582 6642',                  'group' => 'contact',  'label' => 'Phone Number',       'is_public' => true],
            ['key' => 'clinic_whatsapp',     'value' => '201000000000',                      'group' => 'contact',  'label' => 'WhatsApp Number',    'is_public' => true],
            ['key' => 'clinic_email',        'value' => 'info@solyclinic.com',               'group' => 'contact',  'label' => 'Email',              'is_public' => true],
            ['key' => 'clinic_address',      'value' => 'Zahraa Maadi, Cairo, Egypt',           'group' => 'contact',  'label' => 'Address',            'is_public' => true],
            ['key' => 'booking_confirmation','value' => 'auto',                              'group' => 'booking',  'label' => 'Confirmation Mode',  'is_public' => false],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(['key' => $s['key']], array_merge($s, [
                'type'       => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info('✅ Settings seeded');
    }
}
