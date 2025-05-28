<?php
header('Content-Type: application/json');

$targetDir = __DIR__ . '/videos/';
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!isset($_FILES['video'])) {
    echo json_encode(['success' => false, 'message' => 'File video tidak ditemukan']);
    exit;
}

$video = $_FILES['video'];
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if ($video['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $video['error']]);
    exit;
}

// Validasi ekstensi
$allowedExt = ['mp4', 'webm', 'ogg'];
$ext = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt)) {
    echo json_encode(['success' => false, 'message' => 'Format video tidak didukung']);
    exit;
}

// Nama file unik untuk menghindari overwrite
$filename = uniqid() . '.' . $ext;
$targetFile = $targetDir . $filename;

if (!move_uploaded_file($video['tmp_name'], $targetFile)) {
    echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file']);
    exit;
}

// Fungsi update video.json otomatis setelah upload sukses
function updateVideoJson($dir, $jsonFile) {
    $files = array_diff(scandir($dir), array('.', '..'));
    $videos = [];

    foreach ($files as $file) {
        $path = 'videos/' . $file;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
            $videos[] = [
                'title' => pathinfo($file, PATHINFO_FILENAME),
                'file' => $path,
                'date' => date('H:i d-m-Y'),
                'description' => ''
            ];
        }
    }
    file_put_contents($jsonFile, json_encode($videos, JSON_PRETTY_PRINT));
}

updateVideoJson($targetDir, __DIR__ . '/video.json');

echo json_encode(['success' => true, 'message' => 'Video berhasil diupload']);
