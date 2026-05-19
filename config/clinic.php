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

    'name'       => env('CLINIC_NAME',       'Soly Clinic'),
    'name_ar'    => env('CLINIC_NAME_AR',    'عيادة سولي'),
    'tagline'    => env('CLINIC_TAGLINE',    'Premium Dental Care in Nasr City'),
    'phone'      => env('CLINIC_PHONE',      '+20 100 000 0000'),
    'whatsapp'   => env('CLINIC_WHATSAPP',   '201000000000'),
    'email'      => env('CLINIC_EMAIL',      'info@solyclinic.com'),
    'address'    => env('CLINIC_ADDRESS',    'Nasr City, Cairo, Egypt'),
    'address_ar' => env('CLINIC_ADDRESS_AR', 'نصر سيتي، القاهرة، مصر'),

    'instagram'  => env('CLINIC_INSTAGRAM',  ''),
    'facebook'   => env('CLINIC_FACEBOOK',   ''),

    'google_maps_url'   => env('CLINIC_MAPS_URL',   ''),
    'google_maps_embed' => env('CLINIC_MAPS_EMBED',  ''),
    'google_review_url' => env('CLINIC_REVIEW_URL',  ''),

    'booking_advance_days'   => (int) env('BOOKING_ADVANCE_DAYS',    60),
    'booking_slot_duration'  => (int) env('BOOKING_SLOT_DURATION',   30),
    'booking_confirmation'   => env('BOOKING_CONFIRMATION',  'auto'), // auto | manual

];
