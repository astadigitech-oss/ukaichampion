<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format; // Tambahan V4

class CompressImages extends Command
{
    protected $signature = 'image:compress-all';
    protected $description = 'Kompres massal semua gambar lama yang ukurannya raksasa (Support V4)';

    public function handle()
    {
        $manager = ImageManager::usingDriver(Driver::class);

        $files = Storage::disk('public')->files('options');
        $count = 0;

        $this->info('Mencari gambar gajah di server... 🕵️‍♂️');

        foreach ($files as $file) {
            $size = Storage::disk('public')->size($file);

            // Cari gambar yang ukurannya di atas 500 KB
            if ($size > 500000) {

                // FILTER BARU: Cek ekstensi file, pastikan hanya memproses piksel
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($extension, $allowedExtensions)) {
                    $this->line("⏭️ Melewati file vektor/non-piksel: {$file}");
                    continue; // Lewati file ini dan lanjut ke file berikutnya
                }

                $ukuranAwal = round($size / 1024);
                $this->line("Ditemukan gajah: {$file} ({$ukuranAwal} KB) -> Sedang dikompres...");

                $absolutePath = Storage::disk('public')->path($file);

                try {
                    // Kompresi ala V4
                    $image = $manager->decode($absolutePath);
                    $encodedImage = $image->encodeUsingFormat(Format::JPEG, quality: 80);

                    file_put_contents($absolutePath, (string) $encodedImage);
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Gagal kompres {$file}: " . $e->getMessage());
                }
            }
        }

        if ($count > 0) {
            $this->info("✨ OPERASI SUKSES! {$count} gambar berhasil dilangsingkan! ✨");
        } else {
            $this->info("Aman! Tidak ditemukan gambar raksasa (di atas 500 KB) yang perlu dikompres.");
        }
    }
}
