<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratePwaIcons extends Command
{
    protected $signature = 'pwa:icons';
    protected $description = 'Genera iconos PWA temporales';

    public function handle()
    {
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        $path = public_path('images/icons');

        foreach ($sizes as $size) {
            $image = imagecreatetruecolor($size, $size);
            $orange = imagecolorallocate($image, 249, 115, 22);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $orange);
            $fontSize = (int)($size * 0.35);
            imagestring($image, 5, (int)($size * 0.25), (int)($size * 0.35), 'QB', $white);
            imagepng($image, $path . '/icon-' . $size . 'x' . $size . '.png');
            imagedestroy($image);
        }

        $this->info('Iconos generados correctamente.');
    }
}
