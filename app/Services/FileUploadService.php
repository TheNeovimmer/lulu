<?php
namespace App\Services;

class FileUploadService {
    private string $baseDir;

    public function __construct(?string $baseDir = null) {
        $this->baseDir = $baseDir ?: __DIR__ . '/../../public/uploads';
    }

    public function upload(array $file, string $subDir = 'ressources', string $prefix = ''): ?string {
        if (empty($file['name'])) return null;

        $uploadDir = $this->baseDir . '/' . $subDir . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = ($prefix ? $prefix . '-' : '') . time() . '.' . $ext;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return '/uploads/' . $subDir . '/' . $filename;
        }

        return null;
    }

    public function uploadAvatar(array $file): ?string {
        $filename = $this->upload($file, 'avatars', 'avatar');
        return $filename ? basename($filename) : null;
    }
}
