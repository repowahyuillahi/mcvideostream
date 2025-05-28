<?php
// upload.php

$targetDir = __DIR__ . '/videos/';
$videoJson = __DIR__ . '/video.json';

if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success' => false, 'message' => 'Upload gagal atau tidak ada file.']);
  exit;
}

$filename = basename($_FILES['video']['name']);
$targetFile = $targetDir . $filename;

if (!move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
  echo json_encode(['success' => false, 'message' => 'Gagal memindahkan file.']);
  exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$date = date('H:i d-m-y');

// Read existing data
$videos = file_exists($videoJson) ? json_decode(file_get_contents($videoJson), true) : [];

$videos[] = [
  'title' => $title,
  'file' => 'videos/' . $filename,
  'date' => $date,
  'description' => $description
];

file_put_contents($videoJson, json_encode($videos, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
