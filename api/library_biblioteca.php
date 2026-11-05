<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/library_biblioteca.php
 * ====================================================================
 */
error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('memory_limit', '512M');
set_time_limit(300);
header('Content-Type: application/json; charset=utf-8');
$firma = chr(83).chr(101).chr(66).chr(97).chr(83); 
header('X-Author: ' . $firma);
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$host_ip = $_POST['host_ip'] ?? $_GET['host_ip'] ?? '';
$port = isset($_POST['port']) ? (int)$_POST['port'] : 2121;
$cache_dir = __DIR__ . '/../user/cache/biblioteca';
if (!file_exists($cache_dir)) { @mkdir($cache_dir, 0777, true); }
$local_db = $cache_dir . '/app.db';

function curl_download_ftp_file($ip, $port, $remote_path, $local_path) {
    $ch = curl_init("ftp://$ip:$port" . $remote_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data !== false && strlen($data) > 0) {
        @file_put_contents($local_path, $data);
        return true;
    }
    return false;
}

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) { return number_format($bytes / 1073741824, 2) . ' GB'; }
    elseif ($bytes >= 1048576) { return number_format($bytes / 1048576, 2) . ' MB'; }
    elseif ($bytes >= 1024) { return number_format($bytes / 1024, 2) . ' KB'; }
    elseif ($bytes > 1) { return $bytes . ' bytes'; }
    return '0 bytes';
}

if ($action === 'sync_catalog') {
    if (empty($host_ip)) { echo json_encode(['status' => 'error', 'msg' => 'Falta IP']); exit; }
    $db_remote = "/system_data/priv/mms/app.db";
    curl_download_ftp_file($host_ip, $port, $db_remote, $local_db);
    if (!file_exists($local_db) || filesize($local_db) == 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Error al leer app.db']); exit;
    }
    $juegosSueltos = [];
    $carpetas = [];
    $ocultos = [];
    $sistema = [];
    $iconQueue = [];
    try {
        chmod($local_db, 0777);
        $pdo = new PDO("sqlite:" . $local_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 'tbl_appbrowse_%' AND name NOT LIKE '%_bak'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $mainTable = '';
        $maxCount = -1;
        foreach ($tables as $t) {
            $count = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
            if ($count > $maxCount) { $maxCount = $count; $mainTable = $t; }
        }
        if ($mainTable) {
            $query = "SELECT titleId, titleName, visible, folderType, parentFolderId, metaDataPath, lastAccessTime, contentSize FROM $mainTable ORDER BY titleName ASC";
            $allApps = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
            foreach ($allApps as $app) {
                $tId = $app['titleId'];
                if ($app['folderType'] == 1 || preg_match('/^[0-9a-fA-F]{8}-/i', $tId) || !empty($app['folderInfo'])) {
                    $carpetas[$tId] = [
                        'id' => $tId,
                        'nombre' => $app['titleName'],
                        'is_folder' => true,
                        'items' => []
                    ];
                }
            }
            foreach ($allApps as $app) {
                $tId = $app['titleId'];
                if (isset($carpetas[$tId])) continue;
                $metaPath = $app['metaDataPath'];
                $isHidden = ($app['visible'] == 0);
                $folderId = $app['parentFolderId'];
                $isSystem = (strpos($tId, 'NPXS') === 0 || strpos($tId, 'IP') === 0 || strpos($metaPath, 'system_ex') !== false);
                $iconPathLocal = 'user/cache/biblioteca/' . $tId . '.png';
                
                $itemData = [
                    'id' => $tId,
                    'nombre' => $app['titleName'],
                    'meta' => $metaPath,
                    'is_system' => $isSystem,
                    'is_folder' => false,
                    'last_played' => $app['lastAccessTime'] ?? '0',
                    'size' => formatSizeUnits($app['contentSize'] ?? 0),
                    'img' => $iconPathLocal . '?v=' . time()
                ];
                $iconQueue[] = ['id' => $tId, 'meta' => $metaPath];
                if ($isHidden) {
                    $ocultos[] = $itemData;
                } elseif (!empty($folderId) && isset($carpetas[$folderId])) {
                    $carpetas[$folderId]['items'][] = $itemData;
                } elseif ($isSystem) {
                    $sistema[] = $itemData;
                } else {
                    $juegosSueltos[] = $itemData;
                }
            }
        }
        $pdo = null;
        echo json_encode([
            'status' => 'success',
            'catalog' => [
                'juegos' => $juegosSueltos,
                'carpetas' => array_values($carpetas),
                'ocultos' => $ocultos,
                'sistema' => $sistema
            ],
            'queue' => $iconQueue
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
    exit;
}
if ($action === 'download_icon') {
    $tId = $_POST['titleId'] ?? '';
    $meta = $_POST['meta'] ?? '';
    if (empty($tId) || empty($meta)) { echo json_encode(['status' => 'error']); exit; }
    $local_icon = $cache_dir . '/' . $tId . '.png';
    if (!file_exists($local_icon) || filesize($local_icon) == 0) {
        $remote_icon = rtrim($meta, '/') . '/icon0.png';
        if (!curl_download_ftp_file($host_ip, $port, $remote_icon, $local_icon)) {
            $fallbacks = [
                "/user/appmeta/$tId/icon0.png",
                "/system_data/priv/appmeta/$tId/icon0.png",
                "/user/app/$tId/sce_sys/icon0.png"
            ];
            foreach ($fallbacks as $f) {
                if (curl_download_ftp_file($host_ip, $port, $f, $local_icon)) break;
            }
        }
    }
    echo json_encode(['status' => 'success']);
    exit;
}
echo json_encode(['status' => 'error']);
exit;
// Developed By SeBaS
?>
