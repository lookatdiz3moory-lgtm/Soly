<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Soly Clinic — Clinic Configuration
    |--------------------------------------------------------------------------
    |
    | All clinic-specific settings read from .env.
    | Access via: config('clinic.phone'), config('clinic.whatsapp'), etc.
    |
    */

    'name'       => env('CLINIC_NAME',       'Soly Dental Clinic'),
    'name_ar'    => env('CLINIC_NAME_AR',    'عيادات السليم لطب وتجميل الاسنان'),
    'tagline'    => env('CLINIC_TAGLINE',    'Premium Dental Care in Zahraa Maadi'),
    'phone'      => env('CLINIC_PHONE',      '+20 100 582 6642'),
    'whatsapp'   => env('CLINIC_WHATSAPP',   '201005826642'),
    'email'      => env('CLINIC_EMAIL',      'info@solyclinic.com'),
    'address'    => env('CLINIC_ADDRESS',    'Zahraa Maadi'),
    'address_ar' => env('CLINIC_ADDRESS_AR', 'زهراء المعادي، القاهرة'),

    'instagram'  => env('CLINIC_INSTAGRAM',  'https://www.instagram.com/solydentalclinic/'),
    'facebook'   => env('CLINIC_FACEBOOK',   ''),

    'google_maps_url'   => env('CLINIC_MAPS_URL',   'https://maps.app.goo.gl/yKxqocKHGM2ZxD2c6'),
    'google_maps_embed' => env('CLINIC_MAPS_EMBED',  'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1727.9554141525362!2d31.334195738686425!3d29.98199259373878!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1458390052bfa87d%3A0xbcc8d3ff342c95e8!2sDoctor%20fly%20dental%20clinic%20by%20dr%20mohamed%20slim!5e0!3m2!1sen!2seg!4v1779289278599!5m2!1sen!2seg'),
    'google_review_url' => env('CLINIC_REVIEW_URL',  'https://www.google.com/maps/place/Doctor+fly+dental+clinic+by+dr+mohamed+slim/@29.9819926,31.3341957,18z/data=!4m8!3m7!1s0x1458390052bfa87d:0xbcc8d3ff342c95e8!8m2!3d29.9819926!4d31.3354832!9m1!1b1!16s%2Fg%2F11wxt2w0db?entry=ttu&g_ep=EgoyMDI2MDUxNy4wIKXMDSoASAFQAw%3D%3D'),

    'booking_advance_days'   => (int) env('BOOKING_ADVANCE_DAYS',    60),
    'booking_slot_duration'  => (int) env('BOOKING_SLOT_DURATION',   30),
    'booking_confirmation'   => env('BOOKING_CONFIRMATION',  'auto'), // auto | manual

];
