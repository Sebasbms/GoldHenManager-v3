<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/stream_image_api.php
 * ====================================================================
 */
error_reporting(0);
@ini_set('display_errors', 0);
@set_time_limit(0);

$ip = $_GET['ip'] ?? '';
$path = urldecode($_GET['path'] ?? '');
$thumb = $_GET['thumb'] ?? '0';
$buffer = $_GET['buffer'] ?? '0';
$port = 2121;

if (!$ip || !$path) { http_response_code(400); exit; }

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$is_video = ($ext === 'mp4');

$partes = explode('/', ltrim($path, '/'));
$partes_codificadas = array_map('rawurlencode', $partes);
$ftp_url = "ftp://$ip:$port/" . implode('/', $partes_codificadas);

$cache_dir = __DIR__ . '/../user/cache/capturas';
if (!is_dir($cache_dir)) { @mkdir($cache_dir, 0777, true); @file_put_contents($cache_dir . '/.nomedia', ''); }

if ($is_video) {
    $hash = md5($path);
    $cache_video = $cache_dir . '/' . $hash . '.mp4';
    $cache_thumb = $cache_dir . '/' . $hash . '_thumb.jpg';

    if ($thumb === '1') {
        if (!file_exists($cache_thumb) || filesize($cache_thumb) == 0) {
            $cmd = "ffmpeg -y -i " . escapeshellarg($ftp_url) . " -ss 00:00:03 -vframes 1 -q:v 2 " . escapeshellarg($cache_thumb) . " 2>&1";
            @shell_exec($cmd);
        }
        if (file_exists($cache_thumb) && filesize($cache_thumb) > 0) {
            ob_end_clean();
            header('Content-Type: image/jpeg');
            header('Cache-Control: max-age=31536000');
            readfile($cache_thumb);
            exit;
        } else {
            http_response_code(404);
            exit;
        }
    }

    if ($buffer === '1') {
        if (!file_exists($cache_video) || filesize($cache_video) == 0) {
            $cmd = "curl -s -o " . escapeshellarg($cache_video) . " " . escapeshellarg($ftp_url) . " > /dev/null 2>&1 &";
            @shell_exec($cmd);
        }
        ob_end_clean();
        echo json_encode(['status' => 'buffering']);
        exit;
    }

    $ch_size = curl_init($ftp_url);
    curl_setopt($ch_size, CURLOPT_NOBODY, true);
    curl_setopt($ch_size, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch_size);
    $file_size = curl_getinfo($ch_size, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch_size);

    if ($file_size <= 0) { http_response_code(404); exit; }

    ob_end_clean();

    $start = 0;
    $end = $file_size - 1;

    if (isset($_SERVER['HTTP_RANGE'])) {
        if (preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches)) {
            $start = intval($matches[1]);
            $end = isset($matches[2]) && is_numeric($matches[2]) ? intval($matches[2]) : $file_size - 1;
        }
        $length = $end - $start + 1;
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$file_size");
    } else {
        $length = $file_size;
        header('HTTP/1.1 200 OK');
    }

    header("Content-Type: video/mp4");
    header("Accept-Ranges: bytes");
    header("Content-Length: " . $length);
    header("Cache-Control: no-cache");

    if (!file_exists($cache_video)) { http_response_code(404); exit; }

    $stream = @fopen($cache_video, 'rb');
    if (!$stream) { http_response_code(500); exit; }

    fseek($stream, $start);
    $bytes_sent = 0;
    $buffer_size = 1024 * 64; 

    while(!connection_aborted() && $bytes_sent < $length) {
        $data = fread($stream, $buffer_size);
        if ($data === false || strlen($data) == 0) {
            clearstatcache(true, $cache_video);
            if (filesize($cache_video) >= $file_size) { break; }
            usleep(200000); 
            fseek($stream, $start + $bytes_sent);
            continue;
        }
        echo $data;
        ob_flush();
        flush();
        $bytes_sent += strlen($data);
    }

    fclose($stream);
    exit;
}

$hash = md5($path);
$cache_file = $cache_dir . '/' . $hash . '.' . $ext;

if (!file_exists($cache_file) || filesize($cache_file) == 0) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ftp_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data && strlen($data) > 0) {
        file_put_contents($cache_file, $data);
    } else {
        http_response_code(404); exit;
    }
}

ob_end_clean();
header('Content-Type: image/' . ($ext === 'png' ? 'png' : 'jpeg'));
header('Cache-Control: max-age=31536000');
readfile($cache_file);
exit;
// Developed By SeBaS
