<?php
// delete.php

$videoJson = __DIR__ . '/video.json';
$targetDir = __DIR__ . '/videos/';

// Ambil input JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['index'])) {
  echo json_encode(['success' => false, 'message' => 'Index tidak diberikan']);
  exit;
}

$index = intval($data['index']);

// Baca data video
$videos = file_exists($videoJson) ? json_decode(file_get_contents($videoJson), true) : [];

if (!isset($videos[$index])) {
  echo json_encode(['success' => false, 'message' => 'Video tidak ditemukan']);
  exit;
}

// Hapus file video fisik jika ada
$filePath = __DIR__ . '/' . $videos[$index]['file'];
if (file_exists($filePath)) {
  unlink($filePath);
}

// Hapus dari array
array_splice($videos, $index, 1);

// Simpan ulang ke video.json
file_put_contents($videoJson, json_encode($videos, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
