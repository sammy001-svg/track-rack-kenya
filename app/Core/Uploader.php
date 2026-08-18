<?php
namespace App\Core;

use RuntimeException;

/**
 * Image upload handling: validates the real MIME type, generates a
 * collision-free filename, and stores under public/uploads/<folder>/.
 */
class Uploader
{
    private array $config;

    public function __construct(array $uploadConfig)
    {
        $this->config = $uploadConfig;
    }

    /**
     * @param array  $file   One entry from $_FILES
     * @param string $folder Sub-directory under public/uploads
     * @return string Web-relative path, e.g. "/uploads/products/abc123.jpg"
     * @throws RuntimeException on any validation failure
     */
    public function store(array $file, string $folder = 'products'): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid upload.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file was selected.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('That file is larger than the server allows.');
            default:
                throw new RuntimeException('The file could not be uploaded.');
        }

        if ($file['size'] > $this->config['max_bytes']) {
            $mb = round($this->config['max_bytes'] / 1048576, 1);
            throw new RuntimeException("Images must be {$mb} MB or smaller.");
        }

        // Trust the file contents, never the client-supplied type.
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->config['mimes'], true)) {
            throw new RuntimeException('Only JPG, PNG, WEBP and GIF images are accepted.');
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => throw new RuntimeException('Unsupported image type.'),
        };

        $folder    = preg_replace('/[^a-z0-9_-]/', '', strtolower($folder)) ?: 'misc';
        $directory = rtrim($this->config['path'], '/\\') . DIRECTORY_SEPARATOR . $folder;

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('The upload directory could not be created.');
        }

        $basename    = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        $basename    = $basename === '' ? 'image' : substr($basename, 0, 60);
        $filename    = $basename . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('The file could not be saved to disk.');
        }

        @chmod($destination, 0644);

        // Best-effort optimisation; a missing GD extension is not an error.
        ImageProcessor::downscale($destination);
        ImageProcessor::makeWebp($destination);

        return $this->config['url'] . '/' . $folder . '/' . $filename;
    }

    /** Delete a previously stored upload, given its web-relative path. */
    public function delete(?string $webPath): void
    {
        if ($webPath === null || $webPath === '') {
            return;
        }

        $prefix = $this->config['url'] . '/';
        if (!str_starts_with($webPath, $prefix)) {
            return; // Not one of ours - leave it alone.
        }

        $relative = substr($webPath, strlen($prefix));

        // Refuse anything that tries to escape the uploads directory.
        if (str_contains($relative, '..')) {
            return;
        }

        $absolute = rtrim($this->config['path'], '/\\') . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if (is_file($absolute)) {
            @unlink($absolute);
        }

        // Remove the generated WebP sibling too.
        $webp = preg_replace('/\.[a-z0-9]+$/i', '', $absolute) . '.webp';
        if ($webp !== $absolute && is_file($webp)) {
            @unlink($webp);
        }
    }

    /** True when a $_FILES entry actually contains an uploaded file. */
    public static function present(?array $file): bool
    {
        return $file !== null
            && isset($file['error'])
            && !is_array($file['error'])
            && $file['error'] !== UPLOAD_ERR_NO_FILE;
    }
}
