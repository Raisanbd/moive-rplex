<?php
// api.php - BDIX Special Version
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
error_reporting(0);

// আপনার দেওয়া BDIX লিংক
$sourceUrl = "https://raw.githubusercontent.com/siam3310/roarzone-test/refs/heads/main/playlist.m3u";

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$content = fetchUrl($sourceUrl);

if (!$content) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to load BDIX Playlist']);
    exit;
}

$lines = explode("\n", $content);
$channels = [];
$currentItem = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    if (strpos($line, '#EXTINF:') !== false) {
        // লোগো খোঁজা
        preg_match('/tvg-logo="([^"]+)"/', $line, $lm);
        $logo = isset($lm[1]) ? $lm[1] : 'https://assets.apk.live/com.roarzone.tvapps--3-icon.png';

        // টাইটেল খোঁজা (কমা এর পরের অংশ)
        $parts = explode(',', $line);
        $title = end($parts);

        $currentItem = [
            'title' => trim($title),
            'logo' => $logo
        ];
    } 
    // লিংক লাইন (http বা rtmp দিয়ে শুরু)
    elseif ((strpos($line, 'http') === 0 || strpos($line, 'rtmp') === 0) && !empty($currentItem)) {
        $currentItem['stream_url'] = $line;
        // আইডি হিসেবে লিংকের হ্যাশ ব্যবহার করা হচ্ছে
        $currentItem['id'] = md5($line);
        
        $channels[] = $currentItem;
        $currentItem = []; // রিসেট
    }
}

if (empty($channels)) {
    echo json_encode(['status' => 'error', 'message' => 'No channels parsed from M3U']);
} else {
    // সার্চ অপশন
    $query = isset($_POST['query']) ? strtolower(trim($_POST['query'])) : '';
    if (!empty($query)) {
        $results = [];
        foreach ($channels as $ch) {
            if (strpos(strtolower($ch['title']), $query) !== false) {
                $results[] = $ch;
            }
        }
        echo json_encode(['status' => 'success', 'data' => ['list' => $results]]);
    } else {
        echo json_encode(['status' => 'success', 'data' => ['list' => $channels]]);
    }
}
?>