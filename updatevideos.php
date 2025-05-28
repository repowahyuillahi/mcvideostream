<?php
$dir = __DIR__ . '/videos';
$files = array_diff(scandir($dir), ['.', '..']);
$videos = [];

date_default_timezone_set('Asia/Jakarta');

foreach ($files as $file) {
    if (is_file("$dir/$file")) {
        $videos[] = [
            "title" => $file,
            "file" => $file,
            "description" => "Auto-generated entry",
            "date" => date('d-m-y H:i', filemtime("$dir/$file"))
        ];
    }
}

file_put_contents('video.json', json_encode($videos, JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'count' => count($videos)]);
?>
