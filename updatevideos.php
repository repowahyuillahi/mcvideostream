<?php
$videoFolder = __DIR__ . '/videos/';
$files = array_diff(scandir($videoFolder), array('.', '..'));
$videos = [];

foreach ($files as $file) {
    $path = $videoFolder . $file;
    if (is_file($path)) {
        $videos[] = [
            "title" => pathinfo($file, PATHINFO_FILENAME),
            "file" => "videos/" . $file,
            "date" => date("H:i d-m-Y", filemtime($path)),
            "description" => "Video otomatis dari folder"
        ];
    }
}

file_put_contents('video.json', json_encode($videos, JSON_PRETTY_PRINT));
echo json_encode(["success" => true]);
?>
