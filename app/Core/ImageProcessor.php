<?php
namespace App\Core;

/**
 * Post-upload image processing: downscale oversized photographs and emit a
 * WebP alongside the original.
 *
 * GD is optional. Where it is unavailable the original file is simply kept
 * as uploaded, so uploads never fail because of a missing extension.
 */
class ImageProcessor
{
    public const MAX_WIDTH  = 1600;
    public const MAX_HEIGHT = 1600;
    public const QUALITY    = 82;

    public static function available(): bool
    {
        return extension_loaded('gd') && function_exists('imagecreatetruecolor');
    }

    public static function webpSupported(): bool
    {
        return self::available() && function_exists('imagewebp');
    }

    /**
     * Downscale in place if the image exceeds the bounds.
     *
     * @param string $absolutePath Path on disk
     * @return bool True when the file was rewritten
     */
    public static function downscale(string $absolutePath, int $maxWidth = self::MAX_WIDTH, int $maxHeight = self::MAX_HEIGHT): bool
    {
        if (!self::available() || !is_file($absolutePath)) {
            return false;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return false;
        }

        [$width, $height] = $info;
        $mime = $info['mime'] ?? '';

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return false;
        }

        $source = self::load($absolutePath, $mime);
        if ($source === null) {
            return false;
        }

        $ratio     = min($maxWidth / $width, $maxHeight / $height);
        $newWidth  = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $target = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF.
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $saved = self::save($target, $absolutePath, $mime);

        imagedestroy($source);
        imagedestroy($target);

        return $saved;
    }

    /**
     * Write a .webp sibling next to the given image.
     *
     * @return string|null Absolute path of the WebP, or null if not produced
     */
    public static function makeWebp(string $absolutePath): ?string
    {
        if (!self::webpSupported() || !is_file($absolutePath)) {
            return null;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false) {
            return null;
        }

        $mime = $info['mime'] ?? '';

        if ($mime === 'image/webp') {
            return null; // already one
        }

        $source = self::load($absolutePath, $mime);
        if ($source === null) {
            return null;
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $webpPath = preg_replace('/\.[a-z0-9]+$/i', '', $absolutePath) . '.webp';
        $ok       = @imagewebp($source, $webpPath, self::QUALITY);

        imagedestroy($source);

        return $ok ? $webpPath : null;
    }

    private static function load(string $path, string $mime)
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/gif'  => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default      => false,
        };

        return $image === false ? null : $image;
    }

    private static function save($image, string $path, string $mime): bool
    {
        return match ($mime) {
            'image/jpeg' => @imagejpeg($image, $path, self::QUALITY),
            'image/png'  => @imagepng($image, $path, 6),
            'image/gif'  => @imagegif($image, $path),
            'image/webp' => function_exists('imagewebp') ? @imagewebp($image, $path, self::QUALITY) : false,
            default      => false,
        };
    }

    /** Human-readable note for the admin UI. */
    public static function statusNote(): string
    {
        if (!self::available()) {
            return 'GD is not enabled on this server, so images are stored exactly as uploaded. '
                 . 'Enable the gd extension in php.ini for automatic resizing.';
        }

        return self::webpSupported()
            ? 'Images over ' . self::MAX_WIDTH . 'px are downscaled automatically and a WebP copy is generated.'
            : 'Images over ' . self::MAX_WIDTH . 'px are downscaled automatically. WebP output is unavailable in this GD build.';
    }
}
