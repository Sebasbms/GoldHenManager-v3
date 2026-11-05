<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/ps4_screenshots_api.php
 * ====================================================================
 */
ob_start();
error_reporting(0);
@ini_set('display_errors', 0);
set_time_limit(300);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/cache.php';

$action = $_POST['action'] ?? '';
$host_ip = $_POST['host_ip'] ?? '';
$cusa = strtoupper(trim($_POST['cusa_id'] ?? ''));
$port = isset($_POST['port']) ? (int)$_POST['port'] : 2121;
$force = $_POST['force'] ?? '0'; 

if (!filter_var($host_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { 
    ob_end_clean(); echo json_encode(['status' => 'error', 'message' => 'Faltan datos de consola.']); 
    exit; 
}

$cache_dir = __DIR__ . '/../user/cache/biblioteca';
$capturas_dir = __DIR__ . '/../user/cache/capturas'; 

if (!file_exists($cache_dir)) { @mkdir($cache_dir, 0777, true); }
if (!file_exists($capturas_dir)) { 
    @mkdir($capturas_dir, 0777, true); 
    @file_put_contents($capturas_dir . '/.nomedia', '');
}

limpiar_cache_antigua($cache_dir);

if ($action === 'download_single_cap') {
    $remote_path = $_POST['remote_path'] ?? '';
    if (!$remote_path) { ob_end_clean(); echo json_encode(['status'=>'error']); exit; }

    $ext = strtolower(pathinfo($remote_path, PATHINFO_EXTENSION));
    
    if ($ext === 'mp4') {
        $hash = md5($remote_path);
        $thumb_path = $capturas_dir . '/' . $hash . '_thumb.jpg';
        if (!file_exists($thumb_path) || filesize($thumb_path) == 0) {
            $ftp_url = "ftp://$host_ip:$port" . implode('/', array_map('rawurlencode', explode('/', $remote_path)));
            $cmd = "ffmpeg -y -i " . escapeshellarg($ftp_url) . " -ss 00:00:02 -vframes 1 -q:v 2 " . escapeshellarg($thumb_path) . " 2>&1";
            @shell_exec($cmd);
        }
        ob_end_clean(); echo json_encode(['status' => 'success']); exit;
    }

    $hash = md5($remote_path);
    $local_path = $capturas_dir . '/' . $hash . '.' . $ext;
    $tmp_path = $local_path . '.tmp';

    if (!file_exists($local_path) || filesize($local_path) == 0) {
        $fp = fopen($tmp_path, 'w+');
        $url = "ftp://$host_ip:$port" . implode('/', array_map('rawurlencode', explode('/', $remote_path)));
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
        $res = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        if ($res && filesize($tmp_path) > 0) {
            rename($tmp_path, $local_path);
            ob_end_clean(); echo json_encode(['status' => 'success']); exit;
        } else {
            @unlink($tmp_path);
            ob_end_clean(); echo json_encode(['status' => 'error', 'message' => 'Error FTP']); exit;
        }
    }
    ob_end_clean(); echo json_encode(['status' => 'success', 'message' => 'Cache']); exit;
}

$is_global = ($action === 'get_all_caps');
$cache_file = $is_global ? $cache_dir . "/galeria_ALL.json" : $cache_dir . "/galeria_{$cusa}.json";

if ($is_global && $force === '0') {
    $archivos_locales = array_diff(scandir($capturas_dir), array('..', '.', '.nomedia'));
    $local_images = [];
    
    foreach ($archivos_locales as $arch) {
        if (strpos($arch, '_thumb.jpg') !== false || strpos($arch, '.tmp') !== false) continue;
        $ext = strtolower(pathinfo($arch, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'mp4'])) {
            $hash = pathinfo($arch, PATHINFO_FILENAME);
            $type = ($ext === 'mp4') ? 'video' : 'image';
            $thumb_url = ($type === 'video') ? "user/cache/capturas/" . $hash . "_thumb.jpg" : "user/cache/capturas/" . $arch;
            $url_final = "user/cache/capturas/" . $arch;
            
            $local_images[] = [
                'name' => $arch,
                'url' => $url_final,
                'thumb_url' => $thumb_url,
                'remote_path' => '',
                'game_id' => 'GLOBAL',
                'type' => $type
            ];
        }
    }
    
    if (count($local_images) > 0) {
        ob_end_clean();
        echo json_encode(['status' => 'success', 'images' => $local_images, 'missing' => []]);
        exit;
    }
}

if ($force === '0' && file_exists($cache_file)) {
    $cached = json_decode(@file_get_contents($cache_file), true);
    if ($cached && isset($cached['status']) && $cached['status'] === 'success') {
        $missing = [];
        $updated_images = [];
        
        foreach ($cached['images'] as $img) {
            $ext = strtolower(pathinfo($img['remote_path'], PATHINFO_EXTENSION));
            if ($ext === 'mp4') {
                $hash = md5($img['remote_path']);
                $thumb_path = $capturas_dir . '/' . $hash . '_thumb.jpg';
                if (!file_exists($thumb_path) || filesize($thumb_path) == 0) {
                    $missing[] = $img['remote_path'];
                    $img['thumb_url'] = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($img['remote_path']) . "&thumb=1";
                } else {
                    $img['thumb_url'] = "user/cache/capturas/" . $hash . "_thumb.jpg";
                }
                $img['url'] = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($img['remote_path']);
                $img['type'] = 'video';
            } else {
                $hash = md5($img['remote_path']);
                $local_path = $capturas_dir . '/' . $hash . '.' . $ext;
                if (file_exists($local_path) && filesize($local_path) > 0) {
                    $img['url'] = "user/cache/capturas/" . $hash . "." . $ext;
                } else {
                    $img['url'] = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($img['remote_path']);
                    $missing[] = $img['remote_path'];
                }
                $img['thumb_url'] = $img['url'];
                $img['type'] = 'image';
            }
            $updated_images[] = $img;
        }
        
        $cached['images'] = $updated_images;
        $cached['missing'] = $missing;

        if ($action === 'count_only') {
            ob_end_clean(); echo json_encode(['status' => 'success', 'count' => count($updated_images)]);
            exit;
        } else {
            ob_end_clean(); echo json_encode($cached); exit;
        }
    }
}

$ch_test = curl_init();
curl_setopt($ch_test, CURLOPT_URL, "ftp://$host_ip:$port/user/");
curl_setopt($ch_test, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_test, CURLOPT_CUSTOMREQUEST, "LIST");
curl_setopt($ch_test, CURLOPT_TIMEOUT, 4);
$test_ftp = curl_exec($ch_test);
curl_close($ch_test);

if ($test_ftp === false) {
    if (file_exists($cache_file)) {
        $cached = json_decode(@file_get_contents($cache_file), true);
        if ($action === 'count_only') { ob_end_clean(); echo json_encode(['status' => 'success', 'count' => count($cached['images'] ?? [])]); }
        else { ob_end_clean(); echo json_encode($cached); }
        exit;
    }
    ob_end_clean(); echo json_encode(['status' => 'error', 'message' => "Consola apagada."]);
    exit;
}

if ($action === 'delete_capture') {
    $remote_path = (string)($_POST['remote_path'] ?? '');
    
    if (empty($remote_path) && isset($_POST['local_url'])) {
        $localUrl = $_POST['local_url'];
        $hash = pathinfo($localUrl, PATHINFO_FILENAME);
        @unlink($capturas_dir . '/' . $hash . '.jpg');
        @unlink($capturas_dir . '/' . $hash . '.jpeg');
        @unlink($capturas_dir . '/' . $hash . '.png');
        @unlink($capturas_dir . '/' . $hash . '.mp4');
        @unlink($capturas_dir . '/' . $hash . '_thumb.jpg');
        ob_end_clean(); echo json_encode(['status' => 'success']); exit;
    }

    $pathParts = explode('/', $remote_path);
    if (in_array('..', $pathParts, true) || preg_match('/[\x00-\x1F\x7F]/', $remote_path)) {
        ob_end_clean(); echo json_encode(['status' => 'error', 'message' => 'Ruta inválida.']); exit;
    }
    
    $ch = curl_init("ftp://$host_ip:$port/");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => 'LIST', CURLOPT_QUOTE => ['DELE ' . $remote_path], CURLOPT_TIMEOUT => 15]);
    $deleted = curl_exec($ch);
    curl_close($ch);
    
    if ($deleted === false) { ob_end_clean(); echo json_encode(['status' => 'error', 'message' => 'Error FTP']); exit; }
    
    $ext = strtolower(pathinfo($remote_path, PATHINFO_EXTENSION));
    $hash = md5($remote_path);
    
    if ($ext !== 'mp4') {
        @unlink($capturas_dir . '/' . $hash . '.' . $ext);
        @unlink($capturas_dir . '/' . $hash . '.tmp');
    } else {
        @unlink($capturas_dir . '/' . $hash . '.mp4');
        @unlink($capturas_dir . '/' . $hash . '_thumb.jpg');
    }
    foreach (glob($cache_dir . '/galeria_*.json') ?: [] as $cache) @unlink($cache);
    
    ob_end_clean(); echo json_encode(['status' => 'success']); exit;
}

function get_ftp_list($ip, $port, $dir) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "ftp://$ip:$port" . rtrim($dir, '/') . '/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "LIST");
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $res = curl_exec($ch);
    curl_close($ch);
    
    $files = [];
    if ($res) {
        $lines = explode("\n", trim($res));
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $parts = preg_split('/\s+/', trim($line), 9);
            if (count($parts) >= 9) {
                $name = trim($parts[8]);
                if ($name === '.' || $name === '..') continue;
                $files[] = ['name' => $name, 'is_dir' => (substr($parts[0], 0, 1) === 'd')];
            }
        }
    }
    return $files;
}

function excavar_multimedia($ip, $port, $dir, &$archivos_media, $profundidad = 0) {
    if ($profundidad > 4) return;
    $items = get_ftp_list($ip, $port, $dir);
    foreach ($items as $item) {
        $ruta_completa = rtrim($dir, '/') . '/' . $item['name'];
        if ($item['is_dir']) {
            excavar_multimedia($ip, $port, $ruta_completa, $archivos_media, $profundidad + 1);
        } else {
            $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'mp4'])) { $archivos_media[] = $ruta_completa; }
        }
    }
}

$base_photo = "/user/av_contents/photo";
$base_video = "/user/av_contents/video/NPXS20001";
$base_sys_photo = "/user/home";
$rutas_cusa = [];

if ($is_global) {
    $items_nivel1_p = get_ftp_list($host_ip, $port, $base_photo);
    $items_nivel1_v = get_ftp_list($host_ip, $port, $base_video);
    
    foreach ($items_nivel1_p as $item1) { if ($item1['is_dir']) { $rutas_cusa[] = "$base_photo/{$item1['name']}"; } }
    foreach ($items_nivel1_v as $item1) { if ($item1['is_dir']) { $rutas_cusa[] = "$base_video/{$item1['name']}"; } }

    $items_home = get_ftp_list($host_ip, $port, $base_sys_photo);
    foreach($items_home as $h) {
        if($h['is_dir']) {
            $rutas_cusa[] = "$base_sys_photo/{$h['name']}/trophy";
            $rutas_cusa[] = "$base_sys_photo/{$h['name']}/mms";
        }
    }

} else {
    if (!$cusa) { ob_end_clean(); echo json_encode(['status' => 'error', 'message' => 'Falta CUSA.']); exit; }
    
    $items_nivel1_p = get_ftp_list($host_ip, $port, $base_photo);
    $items_nivel1_v = get_ftp_list($host_ip, $port, $base_video);

    foreach ($items_nivel1_p as $item1) {
        if ($item1['is_dir']) {
            $ruta1 = "$base_photo/{$item1['name']}";
            if (stripos($item1['name'], $cusa) !== false) {
                $rutas_cusa[] = $ruta1;
            } else {
                $items_nivel2 = get_ftp_list($host_ip, $port, $ruta1);
                foreach ($items_nivel2 as $item2) {
                    if ($item2['is_dir'] && stripos($item2['name'], $cusa) !== false) { $rutas_cusa[] = "$ruta1/{$item2['name']}"; }
                }
            }
        }
    }
    foreach ($items_nivel1_v as $item1) {
        if ($item1['is_dir'] && stripos($item1['name'], $cusa) !== false) {
            $rutas_cusa[] = "$base_video/{$item1['name']}";
        }
    }
}

$capturas = [];
foreach ($rutas_cusa as $ruta) { excavar_multimedia($host_ip, $port, $ruta, $capturas); }

if (empty($capturas)) {
    $resultado_final = ['status' => 'success', 'images' => [], 'missing' => []];
    @file_put_contents($cache_file, json_encode($resultado_final));
    if ($action === 'count_only') { ob_end_clean(); echo json_encode(['status' => 'success', 'count' => 0]); }
    else { ob_end_clean(); echo json_encode(['status' => 'error', 'message' => "Vacio."]); }
    exit;
}

sort($capturas);
$capturas = array_reverse($capturas); 

$images_format = [];
$missing_downloads = [];

foreach ($capturas as $cap) {
    if ($is_global) {
        $parts = explode('/', $cap);
        $name = (count($parts) > 2) ? "[" . $parts[count($parts)-2] . "] " . end($parts) : end($parts);
    } else {
        $name = basename($cap);
    }
    $game_id = preg_match('/CUSA\d{5}/i', $cap, $gameMatch) ? strtoupper($gameMatch[0]) : '';
    $ext = strtolower(pathinfo($cap, PATHINFO_EXTENSION));
    
    if ($ext === 'mp4') {
        $hash = md5($cap);
        $thumb_path = $capturas_dir . '/' . $hash . '_thumb.jpg';
        if (!file_exists($thumb_path) || filesize($thumb_path) == 0) {
            $missing_downloads[] = $cap;
            $thumb_url = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($cap) . "&thumb=1";
        } else {
            $thumb_url = "user/cache/capturas/" . $hash . "_thumb.jpg";
        }
        $url_final = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($cap);
        $type = 'video';
    } else {
        $hash = md5($cap);
        $ruta_fisica_local = $capturas_dir . '/' . $hash . '.' . $ext;
        if (!file_exists($ruta_fisica_local) || filesize($ruta_fisica_local) == 0) {
            $missing_downloads[] = $cap;
            $url_final = "api/stream_image_api.php?ip=$host_ip&path=" . rawurlencode($cap);
        } else {
            $url_final = "user/cache/capturas/" . $hash . "." . $ext;
        }
        $thumb_url = $url_final;
        $type = 'image';
    }

    $images_format[] = [
        'name' => $name,
        'url' => $url_final,
        'thumb_url' => $thumb_url,
        'remote_path' => $cap,
        'game_id' => $game_id,
        'type' => $type
    ];
}

$resultado_final = ['status' => 'success', 'images' => $images_format, 'missing' => $missing_downloads];
@file_put_contents($cache_file, json_encode($resultado_final));

if ($action === 'count_only') {
    ob_end_clean(); echo json_encode(['status' => 'success', 'count' => count($images_format)]);
} else {
    ob_end_clean(); echo json_encode($resultado_final);
}
exit;
// Developed By SeBaS
