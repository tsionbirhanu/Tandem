<?php
// app/Helpers/upload.php
// Secure File Upload Helper for Tandem MVC Application.

if (!function_exists('uploadImageFile')) {
    /**
     * Validates and moves an uploaded image file into public/uploads/{subfolder}/.
     *
     * @param array $file Single file structure from $_FILES (e.g. $_FILES['avatar'])
     * @param string $subfolder Directory under public/uploads/ (e.g. 'avatars' or 'services')
     * @param int $maxSizeBytes Maximum allowed filesize in bytes (default 5MB)
     * @return array Result array with keys: 'success' (bool), 'path' (string|null), 'error' (string|null)
     */
    function uploadImageFile(array $file, string $subfolder = 'services', int $maxSizeBytes = 5242880): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'path' => null, 'error' => 'Invalid upload request parameters.'];
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'path' => null, 'error' => 'No file was uploaded.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'path' => null, 'error' => 'Uploaded file exceeds the maximum allowed limit of 5MB.'];
            default:
                return ['success' => false, 'path' => null, 'error' => 'An unknown file upload error occurred.'];
        }

        // Validate File Size (Max 5MB)
        if ($file['size'] > $maxSizeBytes) {
            $maxMb = number_format($maxSizeBytes / (1024 * 1024), 1);
            return ['success' => false, 'path' => null, 'error' => "File is too large ({$file['size']} bytes). Maximum allowed size is {$maxMb}MB."];
        }

        // Inspect actual MIME type using Fileinfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!array_key_exists($mimeType, $allowedMimeTypes)) {
            return ['success' => false, 'path' => null, 'error' => "Unsupported file type ({$mimeType}). Only JPG, PNG, and WebP images are allowed."];
        }

        $extension = $allowedMimeTypes[$mimeType];

        // Sanitize subfolder name to prevent directory traversal
        $cleanSubfolder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $subfolder);
        $targetDir = BASE_PATH . '/public/uploads/' . $cleanSubfolder;

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                return ['success' => false, 'path' => null, 'error' => 'Failed to create upload destination directory on server.'];
            }
        }

        // Generate unique, collision-resistant filename
        $filename = sprintf('%s_%s.%s', $cleanSubfolder, uniqid(bin2hex(random_bytes(4)), true), $extension);
        $targetPath = $targetDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'path' => null, 'error' => 'Failed to save uploaded file on server.'];
        }

        // Web relative path from public root
        $relativePath = 'uploads/' . $cleanSubfolder . '/' . $filename;

        return [
            'success' => true,
            'path'    => $relativePath,
            'error'   => null,
        ];
    }
}
