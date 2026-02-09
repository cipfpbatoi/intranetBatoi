<?php

namespace Tests\Unit\Services;

use Illuminate\Http\UploadedFile;
use Intranet\Services\Media\ImageService;
use RuntimeException;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    public function test_new_photo_carnet_creates_png_and_returns_filename(): void
    {
        $file = UploadedFile::fake()->image('foto.jpg', 200, 200);
        $dir = sys_get_temp_dir() . '/intranetbatoi_fotos_' . uniqid('', true);

        $path = null;
        try {
            $filename = ImageService::newPhotoCarnet($file, $dir);
            $path = $dir . '/' . $filename;

            $this->assertStringEndsWith('.png', $filename);
            $this->assertFileExists($path);

            $info = getimagesize($path);
            $this->assertSame(68, $info[0]);
            $this->assertSame(90, $info[1]);
        } finally {
            if ($path && file_exists($path)) {
                @unlink($path);
            }
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }
    }

    public function test_update_photo_carnet_creates_directory_and_writes_png(): void
    {
        $file = UploadedFile::fake()->image('foto.png', 150, 150);
        $dir = sys_get_temp_dir() . '/intranetbatoi_fotos_' . uniqid('', true);
        $path = $dir . '/fixed.png';

        try {
            ImageService::updatePhotoCarnet($file, $path);

            $this->assertFileExists($path);

            $info = getimagesize($path);
            $this->assertSame(68, $info[0]);
            $this->assertSame(90, $info[1]);
        } finally {
            if (file_exists($path)) {
                @unlink($path);
            }
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }
    }

    public function test_update_photo_carnet_throws_on_invalid_image(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'img');
        $dir = sys_get_temp_dir() . '/intranetbatoi_fotos_' . uniqid('', true);
        $path = $dir . '/invalid.png';

        file_put_contents($tmp, 'no-image');
        $file = new UploadedFile($tmp, 'invalid.txt', 'text/plain', null, true);

        try {
            $this->expectException(RuntimeException::class);
            ImageService::updatePhotoCarnet($file, $path);
        } finally {
            if (file_exists($tmp)) {
                @unlink($tmp);
            }
            if (file_exists($path)) {
                @unlink($path);
            }
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }
    }
}
