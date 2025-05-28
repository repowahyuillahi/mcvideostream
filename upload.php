<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $file = $_FILES['video'] ?? null;

    if (!$file || $file['error'] !== 0) {
        echo json_encode(['success' => false, 'message' => 'Upload error.']);
        exit;
    }

    // Validasi ekstensi file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'mp4') {
        echo json_encode(['success' => false, 'message' => 'Hanya file MP4 yang diperbolehkan.']);
        exit;
    }

    // Validasi ukuran maksimal 60MB
    if ($file['size'] > 60 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 60MB.']);
        exit;
    }

    // Cek dan baca video.json
    $jsonPath = 'video.json';
    $videos = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

    // Cek duplikat judul
    foreach ($videos as $v) {
        if (strtolower(trim($v['title'])) === strtolower(trim($title))) {
            echo json_encode(['success' => false, 'message' => 'Judul video sudah ada.']);
            exit;
        }
    }

    // Cek duplikat nama file
    $targetDir = 'videos/';
    $targetFile = $targetDir . basename($file['name']);
    if (file_exists($targetFile)) {
        echo json_encode(['success' => false, 'message' => 'File video dengan nama tersebut sudah ada.']);
        exit;
    }

    // Pindahkan file ke folder videos/
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file.']);
        exit;
    }

    // Tambahkan data baru ke video.json
    $videos[] = [
        'title' => $title,
        'file' => basename($file['name']),
        'description' => $description,
        'date' => date('d-m-y H:i')
    ];
    file_put_contents($jsonPath, json_encode($videos, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
