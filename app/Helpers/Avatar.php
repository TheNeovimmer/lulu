<?php
namespace App\Helpers;

class Avatar {
    public static string $uploadDir = '/public/uploads/avatars/';
    public static array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    public static int $maxSize = 2097152; // 2MB

    public static function upload(array $file): ?string {
        if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedTypes)) {
            return null;
        }
        if ($file['size'] > self::$maxSize) {
            return null;
        }

        $filename = 'avatar_' . uniqid() . '.' . $ext;
        $dest = $_SERVER['DOCUMENT_ROOT'] . self::$uploadDir . $filename;

        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        return move_uploaded_file($file['tmp_name'], $dest) ? $filename : null;
    }
}
