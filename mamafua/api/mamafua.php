<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/common.php';
require_once __DIR__ . '/../app/models/Mamafua.php';

$user = requireAuth(['mamafua']);
$profile = Mamafua::byUserId($user['id']);
if (!$profile) {
    jsonResponse(['success' => false, 'message' => 'Profile not found.'], 404);
}

if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 422);
}

$allowed = [
    'id_document' => 'ids',
    'photo' => 'photos',
    'police_clearance' => 'clearance',
];

$type = sanitize($_POST['document_type'] ?? '');
if (!isset($allowed[$type]) || empty($_FILES['document']['tmp_name'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid upload request.'], 422);
}

$file = $_FILES['document'];
$mime = mime_content_type($file['tmp_name']);
$validMimes = ['image/jpeg', 'image/png', 'application/pdf'];
if (!in_array($mime, $validMimes, true) || $file['size'] > 5 * 1024 * 1024) {
    jsonResponse(['success' => false, 'message' => 'Invalid file type or size.'], 422);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$targetDir = __DIR__ . '/../storage/uploads/' . $allowed[$type];
$filename = $type . '_' . $profile['id'] . '_' . time() . '.' . strtolower($ext);
$targetPath = $targetDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    jsonResponse(['success' => false, 'message' => 'Could not save file.'], 500);
}

$relativePath = 'storage/uploads/' . $allowed[$type] . '/' . $filename;
Mamafua::upsertDocument((int) $profile['id'], $type, $relativePath);
jsonResponse(['success' => true, 'message' => 'Document uploaded for verification.']);
