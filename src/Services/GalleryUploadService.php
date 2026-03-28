<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

class GalleryUploadService
{
    private const MAX_FILE_SIZE = 5_242_880;

    private const ALLOWED_MIME_PREFIX = 'image/';

    public function __construct(
        private readonly string $uploadDirectory = __DIR__ . '/../../public/uploads',
        private readonly string $publicPrefix = '/uploads/',
    ) {
    }

    public function storeMany(array $uploadedFiles): array
    {
        if ($uploadedFiles === []) {
            return [];
        }

        if (! is_dir($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0777, true);
        }

        if (! is_writable($this->uploadDirectory)) {
            throw new RuntimeException(sprintf('Upload directory "%s" is not writable.', $this->uploadDirectory));
        }

        $storedPaths = [];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        foreach ($uploadedFiles as $uploadedFile) {
            $this->validateUpload($uploadedFile, $finfo);

            $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            $filename = bin2hex(random_bytes(16));
            $targetName = $extension !== '' ? sprintf('%s.%s', $filename, strtolower($extension)) : $filename;
            $targetPath = $this->uploadDirectory . '/' . $targetName;

            if (! move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                throw new RuntimeException(sprintf('Failed to store uploaded file "%s".', $uploadedFile['name']));
            }

            $storedPaths[] = $this->publicPrefix . $targetName;
        }

        return $storedPaths;
    }

    public function validateExistingPaths(array $paths): array
    {
        $validatedPaths = [];

        foreach ($paths as $path) {
            if (! is_string($path) || ! str_starts_with($path, $this->publicPrefix)) {
                throw new InvalidArgumentException(sprintf(
                    'Gallery path "%s" is invalid. Only uploaded files under "%s" are allowed.',
                    is_scalar($path) ? (string) $path : 'unknown',
                    $this->publicPrefix
                ));
            }

            $relativePath = substr($path, strlen($this->publicPrefix));

            if ($relativePath === '' || str_contains($relativePath, '..')) {
                throw new InvalidArgumentException(sprintf('Gallery path "%s" is invalid.', $path));
            }

            $absolutePath = $this->uploadDirectory . '/' . ltrim($relativePath, '/');

            if (! is_file($absolutePath)) {
                throw new InvalidArgumentException(sprintf('Gallery file "%s" does not exist.', $path));
            }

            $validatedPaths[] = $path;
        }

        return $validatedPaths;
    }

    private function validateUpload(array $uploadedFile, \finfo $finfo): void
    {
        if (($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException(sprintf('Upload failed for file "%s".', $uploadedFile['name'] ?? 'unknown'));
        }

        if (($uploadedFile['size'] ?? 0) > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException(sprintf(
                'File "%s" exceeds the 5MB upload limit.',
                $uploadedFile['name'] ?? 'unknown'
            ));
        }

        $mimeType = $finfo->file($uploadedFile['tmp_name'] ?? '');

        if (! is_string($mimeType) || ! str_starts_with($mimeType, self::ALLOWED_MIME_PREFIX)) {
            throw new InvalidArgumentException(sprintf(
                'File "%s" must be an image.',
                $uploadedFile['name'] ?? 'unknown'
            ));
        }
    }
}
