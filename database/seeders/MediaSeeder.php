<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Gallery;
use App\Models\Service;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDoctorPhotos();
        $this->seedServiceImages();
        $this->seedGallery();
    }

    private function seedDoctorPhotos(): void
    {
        // doctor-1.jpg is HEIF (iPhone format) — not supported by Chrome/Firefox.
        // Using doctor-2.jpg for Dr. Salim as a cross-browser-safe JPEG.
        Doctor::where('name', 'Dr. Salim')->update(['photo' => 'images/doctor-2.jpg']);
    }

    private function seedServiceImages(): void
    {
        $map = [
            'teeth-cleaning'    => 'images/service-cleaning.jpg',
            'scaling-polishing' => 'images/service-cleaning.jpg',
            'veneers'           => 'images/service-veneers.jpg',
            'zircon-crowns'     => 'images/service-veneers.jpg',
            'hollywood-smile'   => 'images/service-whitening.jpg',
            'dental-implants'   => 'images/service-implants.jpg',
            'orthodontics'      => 'images/service-orthodontics.jpg',
            'root-canal'        => 'images/service-root-canal.jpg',
            'teeth-whitening'   => 'images/service-whitening.jpg',
        ];

        foreach ($map as $slug => $path) {
            Service::where('slug', $slug)->update(['image' => $path]);
        }
    }

    private function seedGallery(): void
    {
        $entries = [
            [
                'type'         => 'before_after',
                'title'        => 'Teeth Whitening Transformation',
                'treatment'    => 'Teeth Whitening',
                'before_image' => 'images/white-before-after-1-before.jpg',
                'after_image'  => 'images/white-before-after-1-after.jpg',
                'is_featured'  => true,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Zircon Crown Restoration',
                'treatment'    => 'Zircon Crown',
                'before_image' => 'images/crown-before-after-2-before.png',
                'after_image'  => 'images/crown-before-after-2-after.jpg',
                'is_featured'  => true,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Dental Implant Result',
                'treatment'    => 'Dental Implant',
                'before_image' => 'images/implant-before-after-3-before.png',
                'after_image'  => 'images/implant-before-after-3-after.jpg',
                'is_featured'  => true,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Orthodontic Treatment',
                'treatment'    => 'Orthodontics',
                'before_image' => 'images/orthod-before-after-4-before.jpg',
                'after_image'  => 'images/orthod-before-after-4-after.png',
                'is_featured'  => false,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Root Canal & Crown',
                'treatment'    => 'Root Canal',
                'before_image' => 'images/root-before-after-5-before.jpg',
                'after_image'  => 'images/root-before-after-5-after.jpeg',
                'is_featured'  => false,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Porcelain Veneers',
                'treatment'    => 'Veneers',
                'before_image' => 'images/veneers-before-after-6-before.jpg',
                'after_image'  => 'images/veneers-before-after-6-after.jpg',
                'is_featured'  => true,
                'is_active'    => true,
            ],
            [
                'type'         => 'before_after',
                'title'        => 'Professional Cleaning',
                'treatment'    => 'Teeth Cleaning',
                'before_image' => 'images/clean-before-after-7-before.jpg',
                'after_image'  => 'images/clean-before-after-7-after.jpg',
                'is_featured'  => false,
                'is_active'    => true,
            ],
        ];

        foreach ($entries as $entry) {
            // Only insert if no identical entry exists (idempotent)
            Gallery::firstOrCreate(
                ['before_image' => $entry['before_image']],
                $entry
            );
        }
    }
}
