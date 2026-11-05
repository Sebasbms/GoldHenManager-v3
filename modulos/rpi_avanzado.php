<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/rpi_avanzado.php
 * ====================================================================
 */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

global $hostIp;
$hostIp = explode(':', $_SERVER['HTTP_HOST'] ?? '127.0.0.1')[0];
if ($hostIp === 'localhost' || $hostIp === '127.0.0.1' || empty($hostIp)) {
    $ifconfig = @shell_exec('ifconfig 2>/dev/null');
    if (preg_match('/inet\s+(192\.168\.\d+\.\d+)/', $ifconfig, $m) || preg_match('/inet\s+(10\.\d+\.\d+\.\d+)/', $ifconfig, $m)) {
        $hostIp = $m[1];
    } else {
        $hostIp = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }
}

$carpetaPkgs = '../user/pkgs_rpi'; 
if (!is_dir($carpetaPkgs)) { @mkdir($carpetaPkgs, 0777, true); }

$archivosParaLimpiar = glob($carpetaPkgs . "/*.pkg");
if (is_array($archivosParaLimpiar)) {
    foreach ($archivosParaLimpiar as $arch) {
        $nombreOrig = basename($arch);
        $nombreLimpio = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $nombreOrig);
        $nombreLimpio = preg_replace('/_+/', '_', $nombreLimpio);
        if ($nombreOrig !== $nombreLimpio) { rename($arch, $carpetaPkgs . '/' . $nombreLimpio); }
    }
}

function obtenerBasuraRPI($ipPs4) {
    if(empty($ipPs4)) return [];
    $archivosBasura = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "ftp://{$ipPs4}:2121/data/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
    $list = curl_exec($ch);
    curl_close($ch);

    if($list) {
        $archivos = explode("\n", trim($list));
        foreach($archivos as $archivo) {
            $basename = basename(trim($archivo));
            if(preg_match('/^tmp_.*\.(json|png)$/i', $basename)) {
                $archivosBasura[] = $basename;
            }
        }
    }
    return $archivosBasura;
}

if (isset($_POST['action']) && $_POST['action'] === 'check_ps4_tmp') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $ipPs4 = trim($_POST['ip'] ?? '');
    if(empty($ipPs4)) { echo json_encode(['success'=>false, 'count'=>0]); exit; }
    
    $basura = obtenerBasuraRPI($ipPs4);
    echo json_encode(['success'=>true, 'count'=>count($basura)]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'clean_ps4_tmp') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $ipPs4 = trim($_POST['ip'] ?? '');
    if(empty($ipPs4)) { echo json_encode(['success'=>false, 'message'=>'Falta configurar IP.', 'borrados'=>0]); exit; }
    
    $basuraAntes = obtenerBasuraRPI($ipPs4);
    if(count($basuraAntes) === 0) {
        echo json_encode(['success'=>true, 'message'=>"El directorio /data/ de tu PS4 ya está limpio.", 'borrados'=>0]);
        exit;
    }

    if (function_exists('ftp_connect')) {
        $conn = @ftp_connect($ipPs4, 2121, 3);
        if ($conn) {
            @ftp_login($conn, 'anonymous', '');
            @ftp_pasv($conn, true);
            if (@ftp_chdir($conn, '/data') || @ftp_chdir($conn, 'data')) {
                foreach ($basuraAntes as $file) {
                    @ftp_delete($conn, $file);
                }
            }
            @ftp_close($conn);
        }
    }

    $comandos = ["CWD /data"];
    foreach ($basuraAntes as $file) {
        $comandos[] = "DELE " . $file;
    }
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, "ftp://{$ipPs4}:2121/");
    curl_setopt($ch2, CURLOPT_QUOTE, $comandos);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_TIMEOUT, 4);
    curl_exec($ch2);
    curl_close($ch2);
    
    foreach ($basuraAntes as $file) {
        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_URL, "ftp://{$ipPs4}:2121/data/" . rawurlencode($file));
        curl_setopt($ch3, CURLOPT_CUSTOMREQUEST, "DELE");
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch3, CURLOPT_TIMEOUT, 1);
        curl_exec($ch3);
        curl_close($ch3);
    }

    echo json_encode(['success'=>true, 'message'=>"Se procesó la eliminación de " . count($basuraAntes) . " archivos basura.", 'borrados'=>count($basuraAntes)]);
    exit;
}

function extraerEnlaceDirecto($url) {
    if (preg_match('/\.pkg(\?.*)?$/i', $url) && strpos($url, '/d/') === false && strpos($url, '/file/') === false && strpos($url, 'mediafire.com') === false) return $url;
    $cookieFile = __DIR__ . '/../user/pkgs_rpi/mf_cookie.txt';
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); curl_setopt($ch, CURLOPT_MAXREDIRS, 5); curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile); curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile); curl_setopt($ch, CURLOPT_NOBODY, true); curl_setopt($ch, CURLOPT_HEADER, true);
    curl_exec($ch); $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE); $urlEfectiva = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    if (stripos($contentType, 'text/html') === false) { curl_close($ch); return $urlEfectiva; }
    curl_setopt($ch, CURLOPT_NOBODY, false); $html = curl_exec($ch); curl_close($ch);
    
    if (preg_match('/id="downloadButton"[^>]*href=["\']([^"\']+)["\']/i', $html, $m)) return $m[1];
    if (preg_match('/class="input popsok"[^>]*href=["\']([^"\']+)["\']/i', $html, $m)) return $m[1];
    if (preg_match('/href=["\'](https?:\/\/[a-zA-Z0-9-]+\.mediafire\.com\/[^"\']+?)["\']/i', $html, $m) && !strpos($m[1], 'view')) return $m[1];
    if (preg_match('/class="[^"]*popsok[^"]*"[^>]*href=["\']([^"\']+)["\']/i', $html, $m)) return $m[1];
    if (preg_match('/href=["\']([^"\']+\.pkg)["\']/i', $html, $m)) return $m[1];
    if (preg_match('/\.pkg(\?.*)?$/i', $urlEfectiva) && strpos($urlEfectiva, '/d/') === false) return $urlEfectiva;
    if (preg_match('/\.(rar|zip)(\?.*)?$/i', $urlEfectiva)) return $urlEfectiva;
    return false;
}

if (isset($_POST['action']) && $_POST['action'] === 'analyze_url') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $url = trim($_POST['url'] ?? '');
    if(empty($url)) { echo json_encode(['success'=>false, 'message'=>'URL vacía.']); exit; }

    $directUrl = extraerEnlaceDirecto($url);
    if(!$directUrl) { echo json_encode(['success'=>false, 'message'=>'No se pudo extraer el enlace directo de Mediafire.']); exit; }

    $cookieFile = __DIR__ . '/../user/pkgs_rpi/mf_cookie.txt';
    $ch = curl_init($directUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_HEADER, true); curl_setopt($ch, CURLOPT_NOBODY, true); curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $headers = curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $effUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($size <= 0 && preg_match('/Content-Length:\s*(\d+)/i', $headers, $matches)) { $size = intval($matches[1]); }

    $filename = basename(parse_url($effUrl, PHP_URL_PATH));
    if (preg_match('/Content-Disposition:.*?filename=["\']?([^"\'\r\n]+)/i', $headers, $matches)) { $filename = trim($matches[1]); }
    $filename = urldecode($filename);
    
    $type = 'pkg';
    if (preg_match('/\.(rar|zip)$/i', $filename)) { $type = 'archive'; }

    echo json_encode(['success' => true, 'type' => $type, 'size' => $size, 'direct_url' => $directUrl, 'filename' => $filename]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'download_extract') {
    while (ob_get_level()) { @ob_end_clean(); }
    set_time_limit(0);
    header('Content-Type: application/json');
    $directUrl = trim($_POST['direct_url'] ?? '');
    $filename = trim($_POST['filename'] ?? '');
    $ipPs4 = trim($_POST['ip'] ?? '');
    if (empty($directUrl)) { echo json_encode(['success' => false, 'message' => 'Falta URL.']); exit; }
    
    $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $filename); 
    $filename = preg_replace('/_+/', '_', $filename);
    if (empty($filename)) $filename = 'archivo_' . time() . '.pkg';
    
    $localPath = __DIR__ . '/../user/pkgs_rpi/' . $filename;
    $cookieFile = __DIR__ . '/../user/pkgs_rpi/mf_cookie.txt';
    
    $fp = fopen($localPath, 'w');
    $chD = curl_init($directUrl);
    curl_setopt($chD, CURLOPT_FILE, $fp);
    curl_setopt($chD, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($chD, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($chD, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($chD, CURLOPT_COOKIEFILE, $cookieFile);
    curl_exec($chD); 
    $httpCodeD = curl_getinfo($chD, CURLINFO_HTTP_CODE); 
    curl_close($chD); 
    fclose($fp);

    $isHtml = false; $headerCheck = @file_get_contents($localPath, false, null, 0, 15);
    if ($headerCheck && (stripos($headerCheck, '<html') !== false || stripos($headerCheck, '<!DOC') !== false)) { $isHtml = true; }

    if ($httpCodeD >= 200 && $httpCodeD < 300 && !$isHtml) {
        $extractedPkgs = [];
        $isArchive = preg_match('/\.(rar|zip)$/i', $filename);
        
        $preGlob = glob(__DIR__ . '/../user/pkgs_rpi/*.pkg');
        
        if ($isArchive) {
            if (preg_match('/\.rar$/i', $filename)) { shell_exec("unrar e -o+ -y " . escapeshellarg($localPath) . " " . escapeshellarg(__DIR__ . '/../user/pkgs_rpi/')); } 
            else { shell_exec("unzip -o " . escapeshellarg($localPath) . " -d " . escapeshellarg(__DIR__ . '/../user/pkgs_rpi/')); }
            @unlink($localPath);
            
            $postExt = glob(__DIR__ . '/../user/pkgs_rpi/*.pkg');
            foreach ($postExt as $arch) {
                $nombreOrig = basename($arch);
                $nombreLimpio = preg_replace('/[^A-Za-z0-9\-\.]/', '_', $nombreOrig);
                $nombreLimpio = preg_replace('/_+/', '_', $nombreLimpio);
                if ($nombreOrig !== $nombreLimpio) {
                    rename($arch, __DIR__ . '/../user/pkgs_rpi/' . $nombreLimpio);
                }
            }
        }

        $postGlob = glob(__DIR__ . '/../user/pkgs_rpi/*.pkg');
        if ($isArchive) {
            $extractedPkgs = array_diff($postGlob, $preGlob);
        } else {
            $extractedPkgs = [$localPath];
        }

        $smallFiles = []; $largeFiles = [];

        foreach ($extractedPkgs as $pkgPath) {
            $pName = basename($pkgPath);
            $pSize = filesize($pkgPath);
            $pSizeTxt = ($pSize > 1073741824) ? number_format($pSize / 1073741824, 2) . ' GB' : number_format($pSize / 1048576, 2) . ' MB';
            
            if ($pSize < 1048576) {
                if (!empty($ipPs4)) {
                    $ftp_url = "ftp://{$ipPs4}:2121/data/pkg/" . rawurlencode($pName);
                    $fpFtp = @fopen($pkgPath, 'r');
                    if ($fpFtp) {
                        $chFtp = curl_init(); curl_setopt($chFtp, CURLOPT_URL, $ftp_url); curl_setopt($chFtp, CURLOPT_UPLOAD, 1); curl_setopt($chFtp, CURLOPT_INFILE, $fpFtp); curl_setopt($chFtp, CURLOPT_INFILESIZE, $pSize); curl_setopt($chFtp, CURLOPT_TIMEOUT, 60); curl_setopt($chFtp, CURLOPT_FTP_CREATE_MISSING_DIRS, 1); curl_exec($chFtp); curl_close($chFtp); fclose($fpFtp);
                    }
                }
                $smallFiles[] = ['name' => $pName, 'txt' => $pSizeTxt];
            } else {
                global $hostIp;
                $puertoLocal = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') ? ':' . $_SERVER['SERVER_PORT'] : ':8080';
                $localUrl = "http://{$hostIp}{$puertoLocal}{$_SERVER['SCRIPT_NAME']}/" . rawurlencode($pName);
                $largeFiles[] = ['name' => $pName, 'url' => $localUrl, 'txt' => $pSizeTxt];
            }
        }
        echo json_encode(['success' => true, 'small' => $smallFiles, 'large' => $largeFiles], JSON_UNESCAPED_SLASHES);
    } else {
        @unlink($localPath); echo json_encode(['success' => false, 'message' => 'Error al descargar. Servidor rechazó la conexión.']);
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'install_direct') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $ipPs4 = trim($_POST['ip'] ?? ''); $puertoPs4 = trim($_POST['puerto'] ?? '12801');
    $urls = json_decode($_POST['urls'] ?? '[]', true);
    if (empty($ipPs4) || empty($urls)) { echo json_encode(['success' => false, 'message' => 'Faltan datos.']); exit; }
    
    $paquetesPesados = [];
    foreach ($urls as $u) {
        $u = trim($u);
        $u = str_replace('https://', 'http://', $u);
        if (!preg_match('/\.pkg(\?.*)?$/i', $u)) { $u .= "#/archivo.pkg"; }
        $paquetesPesados[] = $u;
    }

    $rpiUrl = "http://{$ipPs4}:{$puertoPs4}/api/install";
    $payload = json_encode(["type" => "direct", "packages" => $paquetesPesados], JSON_UNESCAPED_SLASHES);
    
    $chRpi = curl_init($rpiUrl);
    curl_setopt($chRpi, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($chRpi, CURLOPT_POST, true); 
    curl_setopt($chRpi, CURLOPT_POSTFIELDS, $payload); 
    curl_setopt($chRpi, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($payload)]); 
    curl_setopt($chRpi, CURLOPT_TIMEOUT, 40); 
    $rpiRespuesta = curl_exec($chRpi); 
    $httpCode = curl_getinfo($chRpi, CURLINFO_HTTP_CODE); 
    curl_close($chRpi);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        $respObj = @json_decode($rpiRespuesta, true);
        if (isset($respObj['status']) && $respObj['status'] === 'fail') {
            echo json_encode(['success' => false, 'message' => "RPI Falló: " . ($respObj['error'] ?? 'Error desconocido')]);
        } else {
            echo json_encode(['success' => true]);
        }
    } else { 
        echo json_encode(['success' => false, 'message' => 'Error RPI HTTP: ' . $httpCode . ' | Info: ' . strip_tags($rpiRespuesta)]); 
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'ftp_upload') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $ipPs4 = trim($_POST['ip'] ?? '');
    $archivo = basename($_POST['archivo_nombre']);
    $ruta = $carpetaPkgs . '/' . $archivo;
    
    if (empty($ipPs4) || !file_exists($ruta)) { echo json_encode(['success' => false, 'message' => 'Falta la IP o el archivo.']); exit; }
    
    $ftp_url = "ftp://{$ipPs4}:2121/data/pkg/" . rawurlencode($archivo);
    $fp = @fopen($ruta, 'r');
    if (!$fp) { echo json_encode(['success' => false, 'message' => 'No se pudo leer archivo local.']); exit; }
    
    $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $ftp_url); curl_setopt($ch, CURLOPT_UPLOAD, 1); curl_setopt($ch, CURLOPT_INFILE, $fp); curl_setopt($ch, CURLOPT_INFILESIZE, filesize($ruta)); curl_setopt($ch, CURLOPT_TIMEOUT, 120); curl_setopt($ch, CURLOPT_FTP_CREATE_MISSING_DIRS, 1);
    $result = curl_exec($ch); $err = curl_error($ch); curl_close($ch); fclose($fp);
    
    if ($result) { echo json_encode(['success' => true, 'message' => "¡Archivo enviado por FTP!"]); } 
    else { echo json_encode(['success' => false, 'message' => 'Error FTP: ' . $err]); }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'delete_pkg') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $archivo = basename($_POST['archivo_nombre']);
    $ruta = $carpetaPkgs . '/' . $archivo;
    if (file_exists($ruta) && unlink($ruta)) { echo json_encode(['success' => true]); } 
    else { echo json_encode(['success' => false, 'message' => 'Error al eliminar.']); }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'rename_pkg') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $viejo = basename($_POST['viejo_nombre']);
    $nuevo = preg_replace('/[^A-Za-z0-9\-\.]/', '_', trim($_POST['nuevo_nombre']));
    $nuevo = preg_replace('/_+/', '_', $nuevo);
    if (!preg_match('/\.pkg$/i', $nuevo)) { $nuevo .= '.pkg'; }
    $ruta_vieja = $carpetaPkgs . '/' . $viejo;
    $ruta_nueva = $carpetaPkgs . '/' . $nuevo;
    if (file_exists($ruta_vieja) && rename($ruta_vieja, $ruta_nueva)) { echo json_encode(['success' => true, 'nuevo_nombre' => $nuevo]); } 
    else { echo json_encode(['success' => false, 'message' => 'Error al renombrar.']); }
    exit;
}

$reqUri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (strpos($reqUri, '/rpi_avanzado.php/') !== false || strpos($reqUri, '/index.php/') !== false) {
    $partes = explode('.php/', $reqUri);
    $archivo = basename(end($partes));
    $filepath = __DIR__ . '/../user/pkgs_rpi/' . $archivo;
    if (!file_exists($filepath) || is_dir($filepath)) { header("HTTP/1.1 404 Not Found"); exit; }
    while (ob_get_level()) { @ob_end_clean(); }
    $size = filesize($filepath);
    $fp = @fopen($filepath, 'rb');
    if (!$fp) { header("HTTP/1.1 500 Internal Server Error"); exit; }
    $start = 0; $end = $size - 1;
    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
        $start = intval($matches[1]);
        if (!empty($matches[2])) { $end = intval($matches[2]); }
        header('HTTP/1.1 206 Partial Content');
        header("Content-Range: bytes $start-$end/$size");
    } else {
        header('HTTP/1.1 200 OK');
    }
    header("Accept-Ranges: bytes");
    header("Content-Length: " . ($end - $start + 1));
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . $archivo . "\"");
    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') { fclose($fp); exit; }
    fseek($fp, $start);
    $bytes_to_read = $end - $start + 1; $p = 0;
    while (!feof($fp) && $p < $bytes_to_read && connection_status() == 0) {
        $read_size = min(1024 * 128, $bytes_to_read - $p);
        $chunk = fread($fp, $read_size); echo $chunk; flush(); $p += strlen($chunk);
    }
    fclose($fp); exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'scan_network') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    set_time_limit(15);
    global $hostIp;
    $parts = explode('.', $hostIp);
    if (count($parts) !== 4 || $hostIp === '127.0.0.1') { echo json_encode(['status' => 'error', 'message' => 'Usa la IP de tu celular.']); exit; }
    $base = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.';
    $puerto = intval($_GET['port'] ?? 12801); 
    $mh = curl_multi_init(); $curls = [];
    for ($i = 2; $i < 255; $i++) {
        $testIp = $base . $i;
        if ($testIp !== $hostIp) {
            $ch = curl_init("http://$testIp:$puerto/api/install");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, '{"type":"test"}'); curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500); 
            curl_multi_add_handle($mh, $ch); $curls[$testIp] = $ch;
        }
    }
    $running = null;
    do { curl_multi_exec($mh, $running); curl_multi_select($mh, 0.1); } while ($running > 0);
    $found_ip = null;
    foreach ($curls as $ip => $ch) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); $respuesta = curl_multi_getcontent($ch);
        if ($httpCode === 200 || strpos($respuesta, 'success') !== false || strpos($respuesta, 'fail') !== false || strpos($respuesta, 'error') !== false) { $found_ip = $ip; break; }
        curl_multi_remove_handle($mh, $ch); curl_close($ch);
    }
    curl_multi_close($mh);
    if ($found_ip) { echo json_encode(['status' => 'success', 'ip' => $found_ip]); } 
    else { echo json_encode(['status' => 'error', 'message' => 'Consola no encontrada. Asegúrate de tener el RPI abierto.']); }
    exit;
}

if (isset($_GET['check_ip'])) {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $ip = trim($_GET['check_ip']); $port = intval($_GET['port'] ?? 12801);
    $ch = curl_init("http://$ip:$port/api/install");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, '{"type":"test"}'); curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
    $respuesta = curl_exec($ch); $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($httpCode === 200 || strpos($respuesta, 'success') !== false || strpos($respuesta, 'fail') !== false || strpos($respuesta, 'error') !== false) { echo json_encode(['status' => 'online']); } 
    else { echo json_encode(['status' => 'offline']); }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'move_pkgs') {
    while (ob_get_level()) { @ob_end_clean(); }
    header('Content-Type: application/json');
    $rutas = ['/storage/emulated/0/Download', '/storage/emulated/0/Downloads']; $movidos = 0;
    foreach($rutas as $ruta) {
        if(is_dir($ruta)) {
            $archivos = glob($ruta . "/*.pkg");
            if($archivos) {
                foreach($archivos as $arch) {
                    $nombreLimpio = preg_replace('/[^A-Za-z0-9\-\.]/', '_', basename($arch));
                    $nombreLimpio = preg_replace('/_+/', '_', $nombreLimpio);
                    if(rename($arch, $carpetaPkgs . '/' . $nombreLimpio)) { $movidos++; }
                }
            }
        }
    }
    echo json_encode(['success' => true, 'movidos' => $movidos]);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'info_pkg') {
    while (ob_get_level()) { @ob_end_clean(); } 
    header('Content-Type: application/json; charset=utf-8');
    try {
        $archivo = basename($_POST['archivo_nombre']); 
        $ruta = $carpetaPkgs . '/' . $archivo;
        $info = ['nombre' => $archivo, 'tamano_txt' => '0 MB', 'tamano_bytes' => 0, 'title_id' => 'Desconocido', 'app_ver' => '---', 'categoria' => 'Desconocido', 'imagen' => null];
        if (file_exists($ruta) && !empty($archivo)) {
            $tamano = filesize($ruta);
            $info['tamano_bytes'] = $tamano;
            $info['tamano_txt'] = ($tamano > 1073741824) ? number_format($tamano / 1073741824, 2) . ' GB' : number_format($tamano / 1048576, 2) . ' MB';
            $fp = @fopen($ruta, 'rb');
            if ($fp) {
                $bufferSFO = fread($fp, 65536); 
                $cusaPos = strpos($bufferSFO, 'CUSA');
                if ($cusaPos !== false) { $info['title_id'] = preg_replace('/[^A-Z0-9-]/', '', substr($bufferSFO, $cusaPos, 9)); }
                $appVerPos = strpos($bufferSFO, 'APP_VER');
                if ($appVerPos !== false) {
                    $zonaApp = substr($bufferSFO, $appVerPos, 30);
                    if (preg_match('/(\d{2}\.\d{2})/', $zonaApp, $m)) { $info['app_ver'] = $m[1]; }
                }
                $catPos = strpos($bufferSFO, 'CATEGORY');
                if ($catPos !== false) {
                    $zonaCat = substr($bufferSFO, $catPos, 20);
                    if (strpos($zonaCat, 'gd') !== false) { $info['categoria'] = 'Juego Base'; }
                    elseif (strpos($zonaCat, 'gp') !== false) { $info['categoria'] = 'Actualización'; }
                    elseif (strpos($zonaCat, 'ac') !== false) { $info['categoria'] = 'DLC'; }
                    elseif (strpos($zonaCat, 'wt') !== false || strpos($zonaCat, 'st') !== false) { $info['categoria'] = 'Tema'; }
                }
                rewind($fp); $bufferImg = fread($fp, 2097152); fclose($fp);
                $pngStart = strpos($bufferImg, "\x89PNG\r\n\x1a\n");
                if ($pngStart !== false) {
                    $pngEnd = strpos($bufferImg, "IEND\xae\x42\x60\x82", $pngStart);
                    if ($pngEnd !== false) {
                        $pngData = substr($bufferImg, $pngStart, ($pngEnd + 8) - $pngStart);
                        $info['imagen'] = 'data:image/png;base64,' . base64_encode($pngData);
                    }
                }
            }
        } else { $info['title_id'] = 'ERROR: ARCHIVO NO EXISTE'; }
        echo json_encode($info);
        exit;
    } catch (Exception $e) { echo json_encode(['error' => $e->getMessage()]); exit; }
}

$archivosLocales = glob($carpetaPkgs . "/*.pkg");
if (is_array($archivosLocales)) { rsort($archivosLocales); } else { $archivosLocales = []; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RPI Sender</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Outfit:wght@300;400;600;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; color: white; margin: 0; padding: 0; background-color: transparent !important; overflow: hidden; }
        
        .glass-panel { background: rgba(10, 14, 28, 0.75); backdrop-filter: blur(16px); border: 1px solid rgba(0, 255, 255, 0.1); box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
        .neon-box-cyan { box-shadow: 0 0 20px rgba(6, 182, 212, 0.15), inset 0 0 10px rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); }
        
        .btn-cyan-gradient { background: linear-gradient(135deg, #0ea5e9, #06b6d4); box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3); border: none; }
        .btn-green-gradient { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); border: none; }
        .btn-purple-gradient { background: linear-gradient(135deg, #9333ea, #d946ef); box-shadow: 0 4px 15px rgba(217, 70, 239, 0.3); border: none; }
        .btn-orange-gradient { background: linear-gradient(135deg, #ea580c, #f43f5e); box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3); border: none; }

        .modal-overlay { background: rgba(3, 4, 11, 0.95); backdrop-filter: blur(10px); } 
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 255, 255, 0.3); border-radius: 4px; }
        
        .btn-list-edit { background: linear-gradient(145deg, #1e3a8a, #1e40af); box-shadow: 0 2px 8px rgba(30, 64, 175, 0.5); }
        .btn-list-del { background: linear-gradient(145deg, #991b1b, #b91c1c); box-shadow: 0 2px 8px rgba(185, 28, 28, 0.5); }

        .font-orbitron { font-family: 'Orbitron', sans-serif; }
        .font-tech { font-family: 'Share Tech Mono', monospace; }

        @keyframes float-hologram {
            0% { transform: perspective(1000px) rotateX(15deg) rotateY(-10deg) translateY(0); box-shadow: -5px 15px 30px rgba(0, 255, 255, 0.4), 0 0 15px rgba(0, 255, 255, 0.2) inset; }
            50% { transform: perspective(1000px) rotateX(-5deg) rotateY(15deg) translateY(-15px); box-shadow: 5px 25px 45px rgba(0, 255, 255, 0.6), 0 0 30px rgba(0, 255, 255, 0.4) inset; }
            100% { transform: perspective(1000px) rotateX(15deg) rotateY(-10deg) translateY(0); box-shadow: -5px 15px 30px rgba(0, 255, 255, 0.4), 0 0 15px rgba(0, 255, 255, 0.2) inset; }
        }
        .holo-float { animation: float-hologram 6s ease-in-out infinite; border: 1px solid rgba(0, 255, 255, 0.5); will-change: transform; transform-style: preserve-3d; }

        @keyframes anim-float-only {
            0%, 100% { transform: translateY(0); box-shadow: 0 10px 20px rgba(0, 255, 255, 0.2); }
            50% { transform: translateY(-15px); box-shadow: 0 25px 40px rgba(0, 255, 255, 0.5); }
        }
        .anim-float { animation: anim-float-only 4s ease-in-out infinite; border: 1px solid rgba(0, 255, 255, 0.3); }

        @keyframes heartbeat {
            0% { transform: scale(1); box-shadow: 0 0 15px rgba(0,255,255,0.3); }
            15% { transform: scale(1.06); box-shadow: 0 0 35px rgba(0,255,255,0.7); }
            30% { transform: scale(1); box-shadow: 0 0 15px rgba(0,255,255,0.3); }
            45% { transform: scale(1.06); box-shadow: 0 0 35px rgba(0,255,255,0.7); }
            100% { transform: scale(1); box-shadow: 0 0 15px rgba(0,255,255,0.3); }
        }
        .anim-pulse { animation: heartbeat 1.5s ease-in-out infinite; border: 1px solid rgba(0, 255, 255, 0.6); }

        @keyframes swing {
            0%, 100% { transform: perspective(800px) rotateZ(-4deg) rotateY(15deg); box-shadow: -10px 15px 30px rgba(0,255,255,0.3); }
            50% { transform: perspective(800px) rotateZ(4deg) rotateY(-15deg); box-shadow: 10px 15px 30px rgba(0,255,255,0.5); }
        }
        .anim-swing { animation: swing 4s ease-in-out infinite; border: 1px solid rgba(0, 255, 255, 0.4); }

        @keyframes breathe {
            0%, 100% { transform: scale(0.96); box-shadow: 0 5px 15px rgba(0,255,255,0.1); opacity: 0.85; }
            50% { transform: scale(1.08); box-shadow: 0 25px 50px rgba(0,255,255,0.6); opacity: 1; }
        }
        .anim-zoom { animation: breathe 6s ease-in-out infinite; border: 1px solid rgba(0, 255, 255, 0.4); }

        @keyframes anim-spin-3d {
            0% { transform: perspective(1000px) rotateX(15deg) rotateY(0deg); box-shadow: 0 20px 30px rgba(0,255,255,0.4); }
            50% { transform: perspective(1000px) rotateX(15deg) rotateY(180deg); box-shadow: 0 35px 50px rgba(0,255,255,0.6); }
            100% { transform: perspective(1000px) rotateX(15deg) rotateY(360deg); box-shadow: 0 20px 30px rgba(0,255,255,0.4); }
        }
        .anim-spin { animation: anim-spin-3d 5s linear infinite; border: 1px solid rgba(0, 255, 255, 0.5); transform-style: preserve-3d; }

        .anim-static { border: 1px solid rgba(0, 255, 255, 0.5); box-shadow: 0 15px 30px rgba(0,255,255,0.3); }

        .terminal-text { color: #39ff14; text-shadow: 0 0 5px rgba(57,255,20,0.5); line-height: 1.4; text-align: left; }
        .cursor-blink { animation: blink 1s step-end infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .ray-pulse { animation: ray-glow 1.5s infinite alternate; }
        @keyframes ray-glow { 0% { filter: drop-shadow(0 0 5px #00ffff); } 100% { filter: drop-shadow(0 0 20px #00ffff); } }

        .stellar-container { position: absolute; inset: 0; overflow: hidden; border-radius: 0.75rem; }
        .stellar-grid { position: absolute; inset: 0; background-image: radial-gradient(rgba(0,255,255,0.2) 1px, transparent 1px); background-size: 15px 15px; }
        .radar-sweep { position: absolute; top: 50%; left: 50%; width: 600px; height: 600px; margin-top: -300px; margin-left: -300px; border-radius: 50%; background: conic-gradient(from 0deg, transparent 70%, rgba(6,182,212,0.4) 100%); animation: spin-radar 2.5s linear infinite; will-change: transform; }
        .stellar-star { position: absolute; background: #fff; border-radius: 50%; box-shadow: 0 0 8px #0ff; animation: twinkle 1.5s infinite alternate; }
        @keyframes spin-radar { 100% { transform: rotate(360deg); } }
        @keyframes twinkle { 0% { opacity: 0.3; transform: scale(0.8); } 100% { opacity: 1; transform: scale(1.2); } }
    </style>
</head>
<body class="fixed inset-0 overflow-hidden flex flex-col">

    <div id="toast-notification-bottom" class="fixed bottom-12 left-1/2 -translate-x-1/2 z-[100] w-auto min-w-[200px] max-w-[300px] transition-all duration-500 transform translate-y-32 opacity-0 pointer-events-none cursor-pointer">
        <div id="toast-bg-bottom" class="glass-panel p-2.5 rounded-xl flex items-center gap-3 border border-cyan-500/30">
            <div id="toast-icon-bg-bottom" class="w-7 h-7 rounded-full flex items-center justify-center shrink-0">
                <i id="toast-icon-bottom" class="text-sm"></i>
            </div>
            <div class="flex-1 overflow-hidden pr-2">
                <h4 id="toast-title-bottom" class="text-[10px] font-orbitron font-black uppercase tracking-widest text-white leading-tight">Titulo</h4>
                <p id="toast-msg-bottom" class="text-[8px] font-tech text-gray-300 mt-0.5 leading-relaxed whitespace-pre-line">Mensaje</p>
            </div>
        </div>
    </div>

    <div id="toast-interactive-bottom" class="fixed bottom-12 left-1/2 -translate-x-1/2 z-[105] w-[90%] max-w-[320px] transition-all duration-500 transform translate-y-32 opacity-0 pointer-events-none">
        <div class="glass-panel p-3 rounded-xl flex flex-col gap-2 border border-yellow-500/50 shadow-[0_0_20px_rgba(234,179,8,0.2)]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 bg-yellow-900/50 text-yellow-400">
                    <i class="fa-solid fa-broom text-sm animate-pulse"></i>
                </div>
                <div class="flex-1 pr-1">
                    <h4 class="text-[10px] font-orbitron font-black uppercase tracking-widest text-yellow-400 leading-tight">BASURA DETECTADA</h4>
                    <p id="toast-interactive-msg" class="text-[9px] font-tech text-gray-300 mt-1 leading-relaxed">...</p>
                </div>
            </div>
            <div class="flex gap-2 mt-1">
                <button onclick="cerrarToastInteractivo()" class="flex-1 bg-white/5 border border-white/10 active:scale-95 text-gray-300 font-orbitron text-[9px] py-2 rounded-lg transition-transform">OMITIR</button>
                <button onclick="confirmarLimpiezaBasura()" class="flex-1 btn-orange-gradient active:scale-95 text-white font-orbitron text-[9px] py-2 rounded-lg transition-transform shadow-lg">LIMPIAR</button>
            </div>
        </div>
    </div>

    <div id="modal-anim" class="fixed inset-0 z-[110] modal-overlay flex flex-col justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-[250px] mx-auto rounded-3xl p-5 shadow-[0_0_30px_rgba(0,255,255,0.2)] border border-cyan-500/30 transform scale-95 transition-transform duration-300 flex flex-col" id="modal-content-anim">
            <h3 class="text-[12px] font-orbitron font-black text-cyan-400 mb-4 tracking-widest uppercase text-center border-b border-cyan-500/30 pb-2"><i class="fa-solid fa-wand-magic-sparkles mr-2"></i> ANIMACIÓN</h3>
            <div class="flex flex-col gap-2 font-tech text-[11px] text-gray-200">
                <button onclick="changeAnimationPref('holo-float', 'Holograma 3D')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Holograma 3D</button>
                <button onclick="changeAnimationPref('anim-float', 'Flotación Suave')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Flotación Suave</button>
                <button onclick="changeAnimationPref('anim-pulse', 'Latido (Pulso)')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Latido (Pulso)</button>
                <button onclick="changeAnimationPref('anim-swing', 'Balanceo Neón')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Balanceo Neón</button>
                <button onclick="changeAnimationPref('anim-zoom', 'Respiración (Zoom)')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Respiración (Zoom)</button>
                <button onclick="changeAnimationPref('anim-spin', 'Rotación Cuántica')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Rotación Cuántica</button>
                <button onclick="changeAnimationPref('anim-static', 'Estático')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Estático</button>
            </div>
            <button onclick="toggleModal('modal-anim')" class="mt-4 w-full bg-transparent border border-white/10 text-gray-400 hover:text-white font-orbitron text-[9px] py-2.5 rounded-xl transition-colors">CANCELAR</button>
        </div>
    </div>

    <div id="modal-sort" class="fixed inset-0 z-[110] modal-overlay flex flex-col justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-[250px] mx-auto rounded-3xl p-5 shadow-[0_0_30px_rgba(0,255,255,0.2)] border border-cyan-500/30 transform scale-95 transition-transform duration-300 flex flex-col" id="modal-content-sort">
            <h3 class="text-[12px] font-orbitron font-black text-cyan-400 mb-4 tracking-widest uppercase text-center border-b border-cyan-500/30 pb-2"><i class="fa-solid fa-arrow-down-a-z mr-2"></i> ORDENAR POR</h3>
            <div class="flex flex-col gap-2 font-tech text-[11px] text-gray-200">
                <button onclick="aplicarOrden('date_desc')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Más Nuevos</button>
                <button onclick="aplicarOrden('date_asc')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Más Viejos</button>
                <button onclick="aplicarOrden('size_desc')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Mayor Peso</button>
                <button onclick="aplicarOrden('size_asc')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Menor Peso</button>
                <button onclick="aplicarOrden('name_asc')" class="py-2.5 px-3 rounded-xl bg-white/5 hover:bg-cyan-900/40 text-left transition-colors border border-transparent hover:border-cyan-500/30">Nombre (A-Z)</button>
            </div>
            <button onclick="toggleModal('modal-sort')" class="mt-4 w-full bg-transparent border border-white/10 text-gray-400 hover:text-white font-orbitron text-[9px] py-2.5 rounded-xl transition-colors">CANCELAR</button>
        </div>
    </div>

    <div id="modal-history" class="fixed inset-0 z-[110] modal-overlay flex flex-col justify-end md:justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-sm mx-auto rounded-[2rem] p-5 shadow-2xl transition-transform duration-300 translate-y-full md:scale-95 border border-cyan-500/30 flex flex-col max-h-[70vh]" id="modal-content-history">
            <div class="flex justify-between items-center mb-4 shrink-0 border-b border-cyan-500/30 pb-3">
                <h2 class="text-[12px] font-orbitron font-black uppercase tracking-widest flex items-center gap-2 text-cyan-400"><i class="fa-solid fa-clock-rotate-left"></i> HISTORIAL ENLACES</h2>
                <button onclick="toggleModal('modal-history')" class="w-7 h-7 bg-white/5 rounded-full flex items-center justify-center active:scale-90 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="history-list" class="flex-1 overflow-y-auto custom-scrollbar font-tech space-y-1 pr-2 mb-4"></div>
            <button onclick="limpiarHistorial()" class="w-full shrink-0 btn-list-del active:scale-95 text-white font-orbitron font-black text-[10px] py-3 rounded-xl tracking-widest uppercase transition-transform shadow-lg flex justify-center items-center gap-2">
                <i class="fa-solid fa-trash"></i> LIMPIAR HISTORIAL
            </button>
        </div>
    </div>

    <div id="modal-progress" class="fixed inset-0 z-[140] modal-overlay flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 p-4">
        <div class="glass-panel w-full max-w-xs mx-auto rounded-3xl p-6 border border-cyan-500/50 shadow-[0_0_30px_rgba(0,255,255,0.3)] flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-full bg-cyan-900/50 border border-cyan-500/50 flex items-center justify-center mb-5 shadow-[0_0_15px_rgba(0,255,255,0.4)]">
                <i class="fa-solid fa-cloud-arrow-down text-cyan-400 text-3xl animate-bounce"></i>
            </div>
            <h3 class="text-[14px] font-orbitron font-black text-cyan-400 mb-3 tracking-widest uppercase">DESCARGANDO Y EXTRAYENDO</h3>
            <p class="text-[12px] font-tech font-bold text-cyan-200 mb-2 animate-pulse">Procesando en segundo plano...</p>
            <p class="text-[10px] font-sans text-gray-400">Puede demorar si es muy pesado. No cierres la ventana.</p>
        </div>
    </div>

    <div id="modal-alert-center" class="fixed inset-0 z-[130] modal-overlay flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-sm mx-auto rounded-3xl p-5 border shadow-[0_0_30px_rgba(0,0,0,0.5)] transform scale-95 transition-transform duration-300 flex flex-col items-center text-center" id="modal-content-alert-center">
            <div id="alert-center-icon-bg" class="w-14 h-14 rounded-full flex items-center justify-center mb-3 shadow-lg">
                <i id="alert-center-icon" class="text-2xl animate-pulse"></i>
            </div>
            <h3 id="alert-center-title" class="text-[14px] font-orbitron font-black mb-3 tracking-widest uppercase leading-tight">RESULTADO DE EXTRACCIÓN</h3>
            <div id="alert-center-msg" class="text-[12px] font-sans text-gray-200 mb-5 leading-relaxed text-left bg-[#080b14] p-4 rounded-xl border border-white/10 w-full max-h-[50vh] overflow-y-auto custom-scrollbar"></div>
            <div class="flex gap-2 w-full">
                <button id="alert-center-btn-cancel" onclick="cerrarAlertCenter(false)" class="flex-1 bg-white/5 border border-white/10 active:scale-95 text-gray-300 font-orbitron font-black text-[11px] py-3.5 rounded-xl tracking-widest uppercase transition-transform">
                    CERRAR
                </button>
                <button id="alert-center-btn-ok" onclick="cerrarAlertCenter(true)" class="flex-1 btn-cyan-gradient active:scale-95 text-white font-orbitron font-black text-[11px] py-3.5 rounded-xl tracking-widest uppercase transition-transform shadow-lg hidden">
                    ENVIAR A PS4
                </button>
            </div>
        </div>
    </div>

    <main class="flex-1 overflow-hidden flex flex-col w-full h-full relative z-20">
        
        <div class="shrink-0 pt-2 px-3 pb-0">
            
            <div id="main-banner-container" class="relative w-full transition-all duration-500 mb-2" style="height: 60px;">
                
                <div id="banner-default" class="absolute inset-0 w-full h-[60px] glass-panel neon-box-cyan rounded-xl p-2 flex items-center justify-between overflow-hidden transition-opacity duration-300">
                    <div class="absolute -left-4 -top-4 w-20 h-20 bg-cyan-500/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="flex items-center gap-2.5 relative z-10 pl-1">
                        <div class="w-10 h-10 btn-cyan-gradient rounded-xl flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(6,182,212,0.4)]">
                            <i class="fa-solid fa-paper-plane text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-[13px] font-orbitron font-black tracking-widest text-white leading-none drop-shadow-[0_0_5px_rgba(0,255,255,0.5)]">RPI SENDER</h2>
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <div id="ui-dot" class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
                                <span id="status-txt-head" class="text-[8px] font-tech font-bold text-red-400 uppercase tracking-widest leading-none">Desconectada</span>
                            </div>
                        </div>
                    </div>
                    
                    <div id="btn-radar" onclick="conectarConsolaDirecto()" class="z-10 w-[55px] h-[45px] rounded-xl border border-cyan-500/50 bg-[#0a0f1c] shadow-[inset_0_0_15px_rgba(0,255,255,0.1)] flex flex-col items-center justify-center cursor-pointer active:scale-95 transition-all hover:bg-cyan-900/30 mr-0.5">
                        <i id="radar-icon" class="fa-solid fa-bolt text-cyan-400 text-lg mb-0.5 transition-all"></i>
                        <span class="text-[6px] font-orbitron font-bold text-cyan-400 tracking-widest uppercase">SCAN</span>
                    </div>
                </div>

                <div id="banner-scan" class="absolute inset-0 w-full h-[90px] glass-panel rounded-xl p-2 overflow-hidden bg-[#02040a] opacity-0 pointer-events-none transition-opacity duration-300 border border-green-500/30">
                    <div class="stellar-container">
                        <div class="stellar-grid"></div>
                        <div class="radar-sweep"></div>
                        <div class="stellar-star" style="top: 20%; left: 15%; width: 3px; height: 3px; animation-delay: 0s;"></div>
                        <div class="stellar-star" style="top: 70%; left: 30%; width: 2px; height: 2px; animation-delay: 0.5s;"></div>
                        <div class="stellar-star" style="top: 40%; left: 80%; width: 4px; height: 4px; animation-delay: 1s;"></div>
                        <div class="stellar-star" style="top: 85%; left: 60%; width: 3px; height: 3px; animation-delay: 0.2s;"></div>
                        <div class="stellar-star" style="top: 10%; left: 90%; width: 2px; height: 2px; animation-delay: 0.8s;"></div>
                    </div>
                    
                    <div class="relative z-10 h-full flex flex-col justify-between p-1">
                        <div class="flex items-center justify-between mb-1 border-b border-cyan-500/30 pb-1 w-full">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-satellite-dish text-cyan-400 text-[10px] ray-pulse"></i>
                                <span class="text-[9px] font-tech text-cyan-400 font-bold tracking-widest uppercase">RADAR CUÁNTICO ACTIVO</span>
                            </div>
                            <svg viewBox="0 0 200 200" class="w-8 h-8 overflow-visible">
                                <g class="core">
                                    <circle cx="100" cy="100" r="16" fill="#ff4500" opacity="0.6"/>
                                    <circle cx="92" cy="92" r="8" fill="#ff9900" />
                                    <circle cx="108" cy="108" r="8" fill="#ff2200" />
                                    <text x="100" y="106" font-family="'Orbitron', sans-serif" font-size="22" font-weight="900" fill="#fff" text-anchor="middle" style="text-shadow: 0 0 5px #fff;">S</text>
                                </g>
                                <g class="orbit1" style="color: #39ff14;"><ellipse cx="100" cy="100" rx="80" ry="25" fill="none" stroke="currentColor" stroke-width="2" opacity="0.8" class="svg-glow"/><circle cx="20" cy="100" r="5" fill="currentColor" class="svg-glow"/></g>
                                <g class="orbit2" style="color: #00ffff;"><ellipse cx="100" cy="100" rx="85" ry="20" fill="none" stroke="currentColor" stroke-width="2" opacity="0.8" class="svg-glow"/><circle cx="185" cy="100" r="5" fill="currentColor" class="svg-glow"/></g>
                                <g class="orbit3" style="color: #ff00ff;"><ellipse cx="100" cy="100" rx="75" ry="30" fill="none" stroke="currentColor" stroke-width="2" opacity="0.8" class="svg-glow"/><circle cx="100" cy="130" r="5" fill="currentColor" class="svg-glow"/></g>
                            </svg>
                        </div>
                        <div id="inline-terminal" class="font-tech terminal-text text-[9px] w-full h-[40px] overflow-hidden flex flex-col justify-end"></div>
                    </div>
                </div>
            </div>

            <div class="w-full glass-panel border border-cyan-500/30 rounded-xl px-2.5 py-2 mb-2 shadow-lg relative flex items-center justify-between">
                <div class="flex items-center gap-2 pl-1">
                    <i class="fa-solid fa-network-wired text-cyan-400 text-[10px]"></i>
                    <div>
                        <div class="text-[6px] font-tech text-gray-400 font-bold tracking-widest uppercase leading-none mb-0.5">IP PlayStation 4</div>
                        <div class="text-[11px] font-mono font-bold tracking-wider text-white leading-none" id="ui-ip-display">No Configurada</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="toggleModal('modal-config')" class="w-7 h-7 rounded-lg bg-cyan-900/30 border border-cyan-500/30 flex items-center justify-center active:scale-90 transition-all text-cyan-400 shadow-[0_0_10px_rgba(0,255,255,0.2)]">
                        <i class="fa-solid fa-gear text-[10px]"></i>
                    </button>
                    <button onclick="alternarPuertoRPI()" id="btn-rpi" class="btn-purple-gradient text-white font-orbitron font-bold text-[8px] px-3 py-1.5 rounded-lg uppercase tracking-widest active:scale-95 transition-all flex items-center gap-1.5 shadow-lg">
                        <i class="fa-solid fa-plug text-[8px]"></i> <span id="txt-rpi">RPI: NOVA</span>
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center mb-1 pl-1 pr-1">
                <h2 class="text-[8px] font-orbitron font-black tracking-widest uppercase text-cyan-400"><i class="fa-solid fa-link mr-1"></i> ENLACE MEDIAFIRE</h2>
            </div>
            
            <div class="mb-2 glass-panel rounded-xl p-1 flex gap-2 border border-cyan-500/30">
                <button onclick="abrirHistorialLinks()" class="btn-cyan-gradient active:scale-95 text-white w-9 rounded-lg flex items-center justify-center shrink-0 shadow-[0_0_10px_rgba(6,182,212,0.3)] bg-cyan-900/50">
                    <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                </button>
                <input type="url" id="manual-url" placeholder="Pegar enlace directo..." class="flex-1 min-w-0 bg-transparent px-1 py-1.5 font-tech text-[10px] text-cyan-100 placeholder-gray-500 outline-none">
                <button onclick="enviarEnlaceManual(this)" class="btn-cyan-gradient active:scale-95 text-white w-9 rounded-lg flex items-center justify-center shrink-0 shadow-[0_0_10px_rgba(6,182,212,0.3)]">
                    <i class="fa-solid fa-paper-plane text-[9px]"></i>
                </button>
            </div>

            <button onclick="moverPkgsDesdeCelular(this)" class="w-full btn-green-gradient text-white font-orbitron font-bold text-[9px] py-2.5 rounded-lg uppercase tracking-widest active:scale-95 transition-transform flex items-center justify-center gap-1.5 shadow-lg">
                <i class="fa-solid fa-download text-[10px]"></i> EXTRAER DE "DESCARGAS"
            </button>
        </div>

        <div class="shrink-0 px-4 pb-1.5 pt-3 flex items-center justify-between border-b border-cyan-500/20">
            <div class="flex items-center gap-1.5 overflow-hidden pr-2">
                <i class="fa-solid fa-folder text-cyan-500 text-[10px] shrink-0"></i>
                <h2 class="text-[9px] font-orbitron font-black tracking-widest uppercase text-white w-full">/USER/PKGS_RPI</h2>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="toggleModal('modal-sort')" class="bg-cyan-900/40 text-[8px] font-tech text-cyan-200 border border-cyan-500/30 rounded py-1 px-2 flex items-center gap-1.5 active:scale-95 transition-transform shadow-inner">
                    <i class="fa-solid fa-arrow-down-a-z"></i> ORDENAR
                </button>
                <span class="text-[7px] font-tech font-bold text-cyan-200 bg-cyan-900/50 px-2 py-0.5 rounded border border-cyan-500/30 shadow-[0_0_10px_rgba(6,182,212,0.2)]">
                    <?= count($archivosLocales) ?> PKGs
                </span>
            </div>
        </div>

        <div id="pkg-list-container" class="flex-1 overflow-y-auto px-3 pt-2 pb-6 custom-scrollbar relative z-10">
            <?php if (empty($archivosLocales)): ?>
                <div class="glass-panel p-6 text-center rounded-2xl border border-white/5 mt-2">
                    <i class="fa-brands fa-space-awesome text-4xl text-cyan-900/50 mb-3"></i>
                    <p class="text-[10px] font-orbitron text-gray-500 tracking-widest">VACÍO</p>
                </div>
            <?php else: ?>
                <?php foreach ($archivosLocales as $archivo): 
                    $nombre = basename($archivo);
                    $tamanoBytes = filesize($archivo);
                    $tamanoMB = number_format($tamanoBytes / 1048576, 1);
                    $fechaMod = filemtime($archivo);
                ?>
                <div class="pkg-item glass-panel p-2 rounded-xl border border-cyan-500/20 flex items-center justify-between mb-2 hover:border-cyan-500/50 transition-colors group shadow-lg" data-name="<?= htmlspecialchars(strtolower($nombre)) ?>" data-size="<?= $tamanoBytes ?>" data-date="<?= $fechaMod ?>">
                    <div class="flex items-center gap-2.5 flex-1 min-w-0 cursor-pointer active:scale-95 transition-transform pl-1" data-filename="<?= htmlspecialchars($nombre) ?>" onclick="abrirLectorPKG(this)">
                        <div class="w-8 h-8 rounded-lg bg-cyan-900/30 text-cyan-500 flex items-center justify-center shrink-0 border border-cyan-500/30">
                            <i class="fa-solid fa-box text-xs"></i>
                        </div>
                        <div class="min-w-0 flex-1 overflow-hidden pr-2">
                            <h4 class="text-[10px] font-tech font-bold text-gray-200 truncate w-full leading-tight" style="white-space: nowrap;"><?= htmlspecialchars($nombre) ?></h4>
                            <span class="text-[8px] font-tech text-cyan-400 mt-0.5 inline-block"><?= $tamanoMB ?> MB</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 pl-1 pr-1 border-l border-cyan-500/20 pl-2">
                        <button onclick="renombrarPKG('<?= htmlspecialchars($nombre) ?>')" class="w-6 h-6 rounded-full btn-list-edit text-white flex items-center justify-center active:scale-90 transition-all shadow-[0_0_5px_rgba(30,64,175,0.5)]"><i class="fa-solid fa-pen text-[8px]"></i></button>
                        <button onclick="borrarPKG('<?= htmlspecialchars($nombre) ?>')" class="w-6 h-6 rounded-full btn-list-del text-white flex items-center justify-center active:scale-90 transition-all shadow-[0_0_5px_rgba(185,28,28,0.5)]"><i class="fa-solid fa-trash text-[8px]"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <div id="modal-confirm" class="fixed inset-0 z-[110] modal-overlay flex flex-col justify-start pt-[28vh] transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-xs mx-auto rounded-2xl p-5 border border-red-500/30 shadow-[0_0_20px_rgba(255,0,0,0.2)] transform scale-95 transition-transform duration-300" id="modal-content-confirm">
            <h3 class="text-[12px] font-orbitron font-black text-red-400 mb-2 tracking-widest"><i class="fa-solid fa-triangle-exclamation mr-2"></i>ELIMINAR ARCHIVO</h3>
            <p id="confirm-msg" class="text-[10px] font-tech text-gray-300 mb-5 break-all"></p>
            <div class="flex gap-2">
                <button id="btn-confirm-cancel" class="flex-1 bg-white/5 hover:bg-white/10 text-gray-300 font-orbitron text-[9px] py-2.5 rounded-lg transition-colors border border-white/10">CANCELAR</button>
                <button id="btn-confirm-ok" class="flex-1 btn-orange-gradient text-white font-orbitron text-[9px] py-2.5 rounded-lg transition-transform active:scale-95">ACEPTAR</button>
            </div>
        </div>
    </div>

    <div id="modal-prompt" class="fixed inset-0 z-[110] modal-overlay flex flex-col justify-start pt-[28vh] transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-xs mx-auto rounded-2xl p-5 border border-cyan-500/30 shadow-[0_0_20px_rgba(0,255,255,0.2)] transform scale-95 transition-transform duration-300" id="modal-content-prompt">
            <h3 class="text-[12px] font-orbitron font-black text-cyan-400 mb-2 tracking-widest"><i class="fa-solid fa-pen mr-2"></i>RENOMBRAR</h3>
            <p class="text-[9px] font-tech text-gray-400 mb-3">Nuevo nombre (sin corchetes ni espacios):</p>
            <input type="text" id="prompt-input" class="w-full bg-black/50 border border-white/10 rounded-lg px-3 py-2.5 text-[11px] font-tech text-white outline-none focus:border-cyan-400 mb-5">
            <div class="flex gap-2">
                <button id="btn-prompt-cancel" class="flex-1 bg-white/5 hover:bg-white/10 text-gray-300 font-orbitron text-[9px] py-2.5 rounded-lg transition-colors border border-white/10">CANCELAR</button>
                <button id="btn-prompt-ok" class="flex-1 btn-cyan-gradient text-white font-orbitron text-[9px] py-2.5 rounded-lg transition-transform active:scale-95">ACEPTAR</button>
            </div>
        </div>
    </div>

    <div id="modal-tutorial" class="fixed inset-0 z-[120] bg-[#020308]/95 backdrop-blur-md flex flex-col justify-center items-center transition-opacity duration-500 opacity-0 pointer-events-none p-2 sm:p-4">
        <div class="glass-panel w-full max-w-[98%] sm:max-w-[550px] mx-auto rounded-3xl p-5 sm:p-6 shadow-2xl transition-transform duration-500 scale-95 border border-cyan-500/50 flex flex-col max-h-[92vh]" id="modal-content-tutorial">
            <div class="text-center mb-4 border-b border-cyan-500/30 pb-3 shrink-0">
                <i class="fa-solid fa-satellite-dish text-4xl text-cyan-400 mb-2 ray-pulse drop-shadow-[0_0_10px_rgba(0,255,255,0.8)]"></i>
                <h2 class="text-[18px] font-orbitron font-black uppercase tracking-widest text-white">SISTEMA INICIADO</h2>
                <p class="text-[11px] font-tech text-cyan-200 tracking-widest mt-1">GUÍA DE OPERACIÓN BÁSICA</p>
            </div>
            
            <div class="font-tech text-gray-300 space-y-3 overflow-y-auto custom-scrollbar pr-2 flex-1">
                <div class="bg-red-900/20 border border-red-500/50 p-3 rounded-xl flex items-center gap-3 shadow-[0_0_15px_rgba(255,0,0,0.2)]">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl animate-pulse"></i>
                    <div>
                        <b class="text-red-500 text-[13px] uppercase tracking-widest block font-orbitron font-black mb-1">Aviso Importante</b>
                        <p class="text-[11px] text-red-200 leading-relaxed">Lee cuidadosamente las siguientes instrucciones para evitar errores de conexión o transferencias fallidas con el sistema de la consola.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-black/40 p-4 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-cyan-900/50 flex items-center justify-center shrink-0 border border-cyan-500/30 text-cyan-400 mt-0.5">
                        <i class="fa-solid fa-bolt text-base"></i>
                    </div>
                    <div class="flex-1">
                        <b class="text-cyan-400 text-[13px] block mb-1.5 tracking-widest">1. CONECTAR CONSOLA</b>
                        <p class="text-[12px] leading-relaxed">Toca el botón <b class="text-white">SCAN</b> arriba a la derecha. El radar cuántico buscará tu PS4 en la red local automáticamente (asegúrate de que el RPI esté abierto). Si el radar falla, usa el ícono de <b class="text-white">Ajustes (Engranaje)</b> para poner la IP de tu PS4 y el puerto (NOVA u ORIG) manualmente.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-black/40 p-4 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-blue-900/50 flex items-center justify-center shrink-0 border border-blue-500/30 text-blue-400 mt-0.5">
                        <i class="fa-solid fa-link text-base"></i>
                    </div>
                    <div class="flex-1">
                        <b class="text-blue-400 text-[13px] block mb-1.5 tracking-widest">2. ENLACE MEDIAFIRE</b>
                        <p class="text-[12px] leading-relaxed">Pega un link de descarga directa de <b class="text-blue-200">Mediafire</b> que termine obligatoriamente en <b class="text-white">.pkg</b> o en un archivo comprimido <b class="text-white">.rar / .zip</b> y pulsa el botón de enviar. El sistema se encargará de extraerlo y enviarlo automáticamente.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-black/40 p-4 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-green-900/50 flex items-center justify-center shrink-0 border border-green-500/30 text-green-400 mt-0.5">
                        <i class="fa-solid fa-download text-base"></i>
                    </div>
                    <div class="flex-1">
                        <b class="text-green-400 text-[13px] block mb-1.5 tracking-widest">3. EXTRAER DESCARGAS</b>
                        <p class="text-[12px] leading-relaxed">Si descargaste un juego en la memoria de tu teléfono, toca el botón verde <b class="text-white">EXTRAER</b>. El sistema buscará todos los PKGs en tu carpeta 'Download', limpiará los símbolos prohibidos de los nombres y los enviará al servidor interno listo para usar.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-black/40 p-4 rounded-xl border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-purple-900/50 flex items-center justify-center shrink-0 border border-purple-500/30 text-purple-400 mt-0.5">
                        <i class="fa-solid fa-folder-open text-base"></i>
                    </div>
                    <div class="flex-1">
                        <b class="text-purple-400 text-[13px] block mb-1.5 tracking-widest">4. ARCHIVOS INTERNOS</b>
                        <p class="text-[12px] leading-relaxed">También puedes usar un gestor de archivos para pegar tus PKGs directamente en la carpeta interna <b class="text-white">/user/pkgs_rpi/</b>. Al abrir la app, la interfaz gráfica te permitirá editarlos, borrarlos y ver su carátula 3D y su Title ID.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-black/40 p-4 rounded-xl border border-yellow-500/30 mb-2 shadow-[inset_0_0_15px_rgba(234,179,8,0.1)]">
                    <div class="w-10 h-10 rounded-full bg-yellow-900/50 flex items-center justify-center shrink-0 border border-yellow-500/30 text-yellow-400 mt-0.5">
                        <i class="fa-solid fa-server text-base"></i>
                    </div>
                    <div class="flex-1">
                        <b class="text-yellow-400 text-[13px] block mb-1.5 tracking-widest">5. ARCHIVOS LIGEROS (FTP)</b>
                        <p class="text-[12px] leading-relaxed text-yellow-100/80">Los archivos <b class="text-white">menores a 1MB</b> (como temas o mods) no utilizan el Remote Package Installer. Pasan de forma totalmente silenciosa y automática por <b class="text-yellow-400">FTP</b> a la consola. Quedarán guardados en <code class="bg-black/50 text-yellow-400 px-1.5 py-0.5 rounded border border-yellow-500/30 text-[10px]">/data/pkg/</code> listos para instalarse manualmente desde el menú GoldHEN de tu PS4.</p>
                    </div>
                </div>

            </div>
            
            <button onclick="cerrarTutorial()" class="w-full mt-4 shrink-0 btn-cyan-gradient active:scale-95 text-white font-orbitron font-black text-[13px] py-4 rounded-xl tracking-widest uppercase transition-transform shadow-[0_0_15px_rgba(0,255,255,0.4)]">
                ENTENDIDO, EMPEZAR
            </button>
        </div>
    </div>

    <div id="modal-config" class="fixed inset-0 z-[80] modal-overlay flex flex-col justify-end md:justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
        <div class="glass-panel w-full max-w-sm mx-auto rounded-[2rem] p-6 shadow-2xl transition-transform duration-300 translate-y-full md:scale-95 border border-cyan-500/30" id="modal-content-config">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[14px] font-orbitron font-black uppercase tracking-widest flex items-center gap-2 text-white"><i class="fa-solid fa-gear text-cyan-400"></i> AJUSTES</h2>
                <button onclick="toggleModal('modal-config')" class="w-8 h-8 bg-white/5 rounded-full flex items-center justify-center active:scale-90 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="text-[10px] font-tech text-cyan-500 tracking-widest mb-2 pl-1">IP PlayStation 4</div>
            <div class="grid grid-cols-5 gap-2 mb-4">
                <div class="col-span-3 bg-black/60 border border-white/10 rounded-xl overflow-hidden flex items-center px-3 focus-within:border-cyan-400 transition-colors shadow-inner">
                    <i class="fa-solid fa-gamepad text-gray-500 text-sm"></i>
                    <input type="text" id="ip-manual-input" placeholder="192.168.0.x" class="w-full bg-transparent text-white font-tech text-[14px] py-3.5 pl-2 outline-none">
                </div>
                <div class="col-span-2 bg-black/60 border border-white/10 rounded-xl overflow-hidden relative flex items-center focus-within:border-cyan-400 transition-colors shadow-inner">
                    <select id="puerto-manual-input" class="w-full bg-transparent text-white font-tech text-[12px] font-bold py-3.5 pl-2 pr-6 outline-none appearance-none cursor-pointer">
                        <option value="12800">12800</option>
                        <option value="12801" selected>12801</option>
                    </select>
                </div>
            </div>

            <div class="text-[10px] font-tech text-cyan-500 tracking-widest mb-2 pl-1 mt-4">Animación de Carátula</div>
            <button onclick="toggleModal('modal-anim')" class="w-full bg-black/60 border border-white/10 rounded-xl py-3 px-4 flex justify-between items-center text-white font-tech text-[12px] shadow-inner mb-5 hover:border-cyan-400 transition-colors active:scale-95">
                <span id="current-anim-text">Holograma 3D</span>
                <i class="fa-solid fa-chevron-down text-gray-500 text-[10px]"></i>
            </button>
            
            <div class="flex gap-2 mb-4">
                <button onclick="guardarConfigManual()" class="flex-1 btn-cyan-gradient active:scale-95 text-white font-orbitron font-black text-[10px] py-3.5 rounded-xl tracking-widest uppercase flex items-center justify-center gap-2 transition-transform shadow-lg">
                    <i class="fa-solid fa-floppy-disk text-sm"></i> Guardar
                </button>
            </div>

            <div class="border-t border-white/10 pt-4 mb-4">
                <button id="btn-clean-tmp" onclick="limpiarBasuraPS4(true)" class="w-full bg-red-900/30 border border-red-500/50 text-red-400 hover:bg-red-900/50 active:scale-95 font-orbitron font-black text-[10px] py-3 rounded-xl tracking-widest uppercase transition-all flex items-center justify-center gap-2 shadow-[0_0_10px_rgba(239,68,68,0.2)]">
                    <i class="fa-solid fa-broom"></i> LIMPIAR BASURA (TMP) EN PS4
                </button>
            </div>

            <button onclick="abrirTutorialDesdeConfig()" class="w-full bg-transparent border border-orange-500/50 text-orange-400 hover:bg-orange-900/30 active:scale-95 font-orbitron font-black text-[10px] py-3 rounded-xl tracking-widest uppercase transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-book text-sm"></i> VER TUTORIAL DE INICIO
            </button>
        </div>
    </div>

    <div id="modal-preview-pkg" class="fixed inset-0 z-[60] modal-overlay transition-opacity duration-300 flex flex-col justify-end opacity-0 pointer-events-none p-2 sm:p-4">
        <div class="glass-panel w-full max-w-[95%] sm:max-w-md mx-auto rounded-[2rem] border border-cyan-500/30 shadow-2xl transition-transform duration-300 translate-y-full flex flex-col overflow-hidden" id="modal-content-preview">
            
            <div class="flex justify-between items-center px-4 pt-4 relative z-20 shrink-0">
                <span class="text-[13px] font-orbitron font-black tracking-widest uppercase text-cyan-400 flex items-center gap-2"><i class="fa-solid fa-microchip text-base"></i> LECTOR PKG</span>
                <button onclick="cerrarPreview()" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center active:scale-90 text-gray-400 hover:text-white transition-colors border border-white/5"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="p-4 pt-0 overflow-y-auto flex-1 relative z-10 custom-scrollbar">
                <div id="preview-loader" class="flex flex-col items-center justify-center py-12">
                    <i class="fa-solid fa-circle-notch fa-spin text-5xl text-cyan-500 mb-5 drop-shadow-[0_0_10px_rgba(6,182,212,0.8)]"></i>
                    <p class="text-[10px] font-tech text-cyan-200 tracking-widest">ANALIZANDO CÓDIGO BINARIO...</p>
                </div>

                <div id="preview-data" class="hidden flex-col items-center pb-2">
                    
                    <div id="pkg-cover-container" class="holo-float w-56 h-56 sm:w-64 sm:h-64 mx-auto rounded-[1.5rem] overflow-hidden mb-6 relative bg-[#020617] flex items-center justify-center mt-8 shadow-[0_15px_30px_rgba(0,0,0,0.6)]">
                        <img id="preview-img" src="" class="w-full h-full object-cover hidden shadow-inner">
                        <i id="preview-icon-fallback" class="fa-solid fa-box-open text-7xl text-cyan-900/50"></i>
                    </div>
                    
                    <h2 id="preview-nombre" class="text-[13px] font-tech font-bold text-center text-white break-all mb-5 px-2 leading-tight drop-shadow-md">Nombre.pkg</h2>
                    
                    <div class="w-full bg-black/60 border border-cyan-500/20 rounded-2xl p-4 mb-5 shadow-inner backdrop-blur-sm font-tech">
                        <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-[11px]">
                            <div class="flex flex-col border-r border-white/10 pr-2">
                                <span class="text-[9px] text-gray-500 tracking-widest mb-0.5">TIPO</span>
                                <span class="font-bold text-cyan-400 truncate" id="preview-cat"><i class="fa-brands fa-playstation mr-1"></i> PS4</span>
                            </div>
                            <div class="flex flex-col pl-2">
                                <span class="text-[9px] text-gray-500 tracking-widest mb-0.5">UBICACIÓN</span>
                                <span class="font-bold text-gray-300">/pkgs_rpi/</span>
                            </div>
                            
                            <div class="col-span-2 border-t border-white/10 pt-2"></div>
                            
                            <div class="flex flex-col border-r border-white/10 pr-2">
                                <span class="text-[9px] text-gray-500 tracking-widest mb-0.5">ESTADO</span>
                                <span class="font-bold text-emerald-400"><i class="fa-solid fa-circle-check mr-1"></i> Listo</span>
                            </div>
                            <div class="flex flex-col pl-2" id="container-app-ver">
                                <span class="text-[9px] text-gray-500 tracking-widest mb-0.5">VER APP</span>
                                <span class="font-bold text-purple-300" id="preview-ver">1.00</span>
                            </div>

                            <div class="col-span-2 border-t border-white/10 pt-2"></div>

                            <div class="flex flex-col border-r border-white/10 pr-2">
                                <span class="text-[9px] text-cyan-500/70 tracking-widest mb-0.5">ID TITLE</span>
                                <span class="font-bold text-cyan-300 tracking-wider" id="preview-tid">CUSA00000</span>
                            </div>
                            <div class="flex flex-col pl-2">
                                <span class="text-[9px] text-gray-500 tracking-widest mb-0.5">PESO TOTAL</span>
                                <span class="font-bold text-white tracking-widest" id="preview-size">0 MB</span>
                            </div>
                        </div>
                    </div>

                    <button id="btn-instalar-local" class="w-full active:scale-95 text-white font-orbitron font-black text-[13px] py-4 rounded-2xl tracking-widest uppercase flex items-center justify-center gap-2 transition-transform shadow-lg">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentIP = localStorage.getItem('sebas_ip_final_libre') || localStorage.getItem('ps4_store_ip') || '';
        let currentPort = localStorage.getItem('sebas_port_libre') || localStorage.getItem('ps4_store_puerto') || '12801';
        let currentFileName = ''; 
        let audioCtx;

        document.addEventListener('DOMContentLoaded', () => {
            if (currentPort === '2121') currentPort = '12801'; 
            
            let savedAnimText = localStorage.getItem('ps4_cover_anim_text') || 'Holograma 3D';
            const animTextEl = document.getElementById('current-anim-text');
            if(animTextEl) animTextEl.innerText = savedAnimText;

            actualizarUI();
            
            if (currentIP !== '') {
                document.getElementById('ip-manual-input').value = currentIP;
                document.getElementById('puerto-manual-input').value = currentPort;
                pingConsola(false);
                setTimeout(() => { chequearBasuraPS4Background(); }, 2000);
            }
            if(!localStorage.getItem('ps4_tutorial_v1_seen')) { abrirTutorial(); }
            
            let savedSort = localStorage.getItem('ps4_sort_pref') || 'date_desc';
            ordenarArchivos(savedSort);
        });

        function conectarConsolaDirecto() {
            if(!currentIP) {
                 iniciarEscaneoRed();
            } else {
                 playTechSound('click');
                 if (typeof window.parent.sysNotification === 'function') window.parent.sysNotification("CONECTANDO", "Verificando IP: " + currentIP, "fa-wifi");
                 pingConsola(true);
            }
        }

        function mostrarNotificacion(titulo, mensaje, icono = "fa-check") {
            if (typeof window.parent.sysNotification === 'function') {
                window.parent.sysNotification(titulo, mensaje, icono);
            } else {
                showToast(icono === "fa-check", titulo, mensaje);
            }
        }

        function chequearBasuraPS4Background() {
            if (!currentIP) return;
            const fd = new FormData();
            fd.append('action', 'check_ps4_tmp');
            fd.append('ip', currentIP);
            fetch(window.location.href, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.success && data.count > 0) {
                    mostrarToastInteractivo(`Se encontraron ${data.count} archivos temporales (tmp) en tu consola.`);
                }
            }).catch(e => {});
        }

        function mostrarToastInteractivo(msg) {
            const toast = document.getElementById('toast-interactive-bottom');
            document.getElementById('toast-interactive-msg').innerText = msg;
            toast.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');
        }

        function cerrarToastInteractivo() {
            const toast = document.getElementById('toast-interactive-bottom');
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        }

        function confirmarLimpiezaBasura() {
            cerrarToastInteractivo();
            limpiarBasuraPS4(false);
        }

        function changeAnimationPref(val, textName) { 
            localStorage.setItem('ps4_cover_anim', val); 
            localStorage.setItem('ps4_cover_anim_text', textName); 
            document.getElementById('current-anim-text').innerText = textName;
            toggleModal('modal-anim');
        }
        
        function abrirTutorial() { toggleModal('modal-tutorial', 'modal-content-tutorial', 'scale'); }
        function cerrarTutorial() { localStorage.setItem('ps4_tutorial_v1_seen', 'true'); toggleModal('modal-tutorial', 'modal-content-tutorial', 'scale'); }
        function abrirTutorialDesdeConfig() { toggleModal('modal-config'); setTimeout(() => abrirTutorial(), 300); }

        function aplicarOrden(criterio) {
            ordenarArchivos(criterio);
            toggleModal('modal-sort');
        }

        function ordenarArchivos(criterio) {
            const container = document.getElementById('pkg-list-container');
            if(!container) return;
            const items = Array.from(container.getElementsByClassName('pkg-item'));
            if(items.length === 0) return;

            items.sort((a, b) => {
                if (criterio === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
                if (criterio === 'size_desc') return parseInt(b.dataset.size) - parseInt(a.dataset.size);
                if (criterio === 'size_asc') return parseInt(a.dataset.size) - parseInt(b.dataset.size);
                if (criterio === 'date_desc') return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                if (criterio === 'date_asc') return parseInt(a.dataset.date) - parseInt(b.dataset.date);
            });

            items.forEach(item => container.appendChild(item));
            localStorage.setItem('ps4_sort_pref', criterio);
        }

        function guardarLinkHistorial(url) {
            if (!url) return;
            let hist = JSON.parse(localStorage.getItem('ps4_link_history') || '[]');
            hist = hist.filter(h => h !== url); 
            hist.unshift(url); 
            if (hist.length > 20) hist.pop(); 
            localStorage.setItem('ps4_link_history', JSON.stringify(hist));
        }

        function abrirHistorialLinks() {
            const hist = JSON.parse(localStorage.getItem('ps4_link_history') || '[]');
            const container = document.getElementById('history-list');
            if (hist.length === 0) {
                container.innerHTML = '<p class="text-[10px] text-gray-500 text-center py-4">No hay enlaces recientes.</p>';
            } else {
                container.innerHTML = hist.map(url => {
                    let escUrl = url.replace(/'/g, "\\'");
                    return `<div class="flex justify-between items-center p-2.5 border-b border-white/5 hover:bg-white/10 transition-colors cursor-pointer rounded" onclick="seleccionarLink('${escUrl}')">
                        <span class="text-[10px] font-tech text-cyan-100 truncate w-full">${url}</span>
                    </div>`;
                }).join('');
            }
            toggleModal('modal-history', 'modal-content-history');
        }

        function seleccionarLink(url) {
            document.getElementById('manual-url').value = url;
            toggleModal('modal-history', 'modal-content-history');
        }

        function limpiarHistorial() {
            localStorage.removeItem('ps4_link_history');
            abrirHistorialLinks();
            mostrarNotificacion("HISTORIAL", "Enlaces eliminados.", "fa-check");
        }

        function limpiarBasuraPS4(fromConfig = true) {
            if (!currentIP) { 
                mostrarNotificacion("ERROR DE RED", "Configura la IP de tu consola primero.", "fa-wifi");
                return; 
            }
            mostrarNotificacion("LIMPIANDO...", "Buscando archivos tmp en /data/...", "fa-broom");
            
            const btn = document.getElementById('btn-clean-tmp');
            if(btn) { btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> LIMPIANDO...'; btn.disabled = true; }

            const fd = new FormData();
            fd.append('action', 'clean_ps4_tmp');
            fd.append('ip', currentIP);
            
            fetch(window.location.href, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.success) { 
                    mostrarNotificacion("LIMPIEZA FTP", data.message, "fa-check");
                    if(fromConfig) {
                        const modal = document.getElementById('modal-config');
                        if(!modal.classList.contains('opacity-0')) {
                            toggleModal('modal-config'); 
                        }
                    }
                } else { 
                    mostrarNotificacion("ERROR", data.message, "fa-times");
                }
            }).catch(err => { 
                mostrarNotificacion('ERROR', 'Fallo conexión con FTP de PS4.', "fa-times");
            }).finally(() => {
                if(btn) { btn.innerHTML = '<i class="fa-solid fa-broom"></i> LIMPIAR BASURA (TMP) EN PS4'; btn.disabled = false; }
            });
        }

        function actualizarUI() {
            document.getElementById('ui-ip-display').innerText = currentIP !== '' ? currentIP : 'NO CONFIG';
            const btnRpi = document.getElementById('btn-rpi');
            const txtRpi = document.getElementById('txt-rpi');
            if (currentPort === '12801') {
                txtRpi.innerText = 'RPI: NOVA';
                btnRpi.className = "btn-purple-gradient text-white font-orbitron font-bold text-[8px] px-3 py-1.5 rounded-lg uppercase tracking-widest active:scale-95 transition-all flex items-center gap-1.5 shadow-lg";
            } else {
                txtRpi.innerText = 'RPI: ORIG';
                btnRpi.className = "btn-orange-gradient text-white font-orbitron font-bold text-[8px] px-3 py-1.5 rounded-lg uppercase tracking-widest active:scale-95 transition-all flex items-center gap-1.5 shadow-lg";
            }
        }

        function toggleModal(modalID, contentID = null, forcedAnim = null) {
            const modal = document.getElementById(modalID); 
            const content = contentID ? document.getElementById(contentID) : modal.querySelector('div[id^="modal-content"]');
            
            let transformClass = 'translate-y-full';
            if (forcedAnim === 'scale' || modalID.includes('tutorial') || modalID.includes('alert') || modalID.includes('confirm') || modalID.includes('prompt') || modalID.includes('sort') || modalID.includes('anim')) {
                transformClass = 'scale-95';
            }
            
            if (modal.classList.contains('opacity-0')) {
                modal.classList.remove('opacity-0', 'pointer-events-none'); 
                if(content) setTimeout(() => { content.classList.remove(transformClass); }, 10);
            } else {
                if(content) content.classList.add(transformClass); 
                setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 250);
            }
        }

        function initAudio() {
            if(!audioCtx) { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
            if(audioCtx.state === 'suspended') { audioCtx.resume(); }
        }

        function playTechSound(type) {
            if (window.parent && typeof window.parent.playSynth === 'function') {
                if (type === 'click' || type === 'select') { window.parent.playSynth(); return; }
                if (type === 'success') { window.parent.playSynth('success'); return; }
                if (type === 'error') { window.parent.playSynth('error'); return; }
            }
            
            try {
                initAudio(); const osc = audioCtx.createOscillator(); const gain = audioCtx.createGain();
                osc.connect(gain); gain.connect(audioCtx.destination); const now = audioCtx.currentTime;
                if (type === 'scan') {
                    osc.type = 'sine'; osc.frequency.setValueAtTime(880, now); osc.frequency.setValueAtTime(1200, now + 0.1);
                    osc.frequency.setValueAtTime(880, now + 0.2); gain.gain.setValueAtTime(0.03, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3); osc.start(now); osc.stop(now + 0.3);
                } else if (type === 'success') {
                    osc.type = 'square'; osc.frequency.setValueAtTime(600, now); osc.frequency.setValueAtTime(1200, now + 0.1);
                    gain.gain.setValueAtTime(0.03, now); gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    osc.start(now); osc.stop(now + 0.3);
                } else if (type === 'error') {
                    osc.type = 'sawtooth'; osc.frequency.setValueAtTime(150, now); osc.frequency.setValueAtTime(100, now + 0.2);
                    gain.gain.setValueAtTime(0.03, now); gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                    osc.start(now); osc.stop(now + 0.4);
                }
            } catch(e) {}
        }

        function mostrarAlertCenter(title, htmlContent, isFtpOnly = false) {
            playTechSound('success');
            const modal = document.getElementById('modal-alert-center');
            const content = document.getElementById('modal-content-alert-center');
            const iconBg = document.getElementById('alert-center-icon-bg');
            const icon = document.getElementById('alert-center-icon');
            const btnOk = document.getElementById('alert-center-btn-ok');
            const btnCancel = document.getElementById('alert-center-btn-cancel');

            document.getElementById('alert-center-title').innerText = title;
            document.getElementById('alert-center-msg').innerHTML = htmlContent;
            
            if (isFtpOnly) {
                iconBg.className = "w-14 h-14 rounded-full bg-emerald-900/50 border border-emerald-500/50 flex items-center justify-center mb-3 shadow-[0_0_15px_rgba(16,185,129,0.4)]";
                icon.className = "fa-solid fa-check text-emerald-400 text-2xl animate-pulse";
                btnOk.classList.add('hidden');
                btnCancel.innerText = "ENTENDIDO";
            } else {
                iconBg.className = "w-14 h-14 rounded-full bg-cyan-900/50 border border-cyan-500/50 flex items-center justify-center mb-3 shadow-[0_0_15px_rgba(6,182,212,0.4)]";
                icon.className = "fa-solid fa-list-check text-cyan-400 text-2xl animate-pulse";
                btnOk.classList.remove('hidden');
                btnCancel.innerText = "CANCELAR";
            }

            modal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => content.classList.remove('scale-95'), 10);
        }

        function cerrarAlertCenter(confirmRpi = false) {
            const modal = document.getElementById('modal-alert-center');
            const content = document.getElementById('modal-content-alert-center');
            content.classList.add('scale-95');
            setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 200);

            if (confirmRpi && window.pendingRpiUrls && window.pendingRpiUrls.length > 0) {
                let urls = window.pendingRpiUrls;
                window.pendingRpiUrls = null;
                enviarRpiDirecto(urls);
            } else {
                setTimeout(() => location.reload(), 300);
            }
        }

        function showToast(isSuccess, title, msg) {
            if(isSuccess) playTechSound('success'); else playTechSound('error');

            const toastBg = document.getElementById('toast-bg-bottom'); 
            const toastIconBg = document.getElementById('toast-icon-bg-bottom'); 
            const toastIcon = document.getElementById('toast-icon-bottom'); 
            const toast = document.getElementById('toast-notification-bottom');
            
            if(isSuccess) { 
                toastBg.className = "glass-panel p-2.5 rounded-xl flex items-center gap-3 border border-cyan-500/30 shadow-[0_0_15px_rgba(0,255,255,0.2)]"; 
                toastIconBg.className = "w-7 h-7 rounded-full flex items-center justify-center shrink-0 bg-cyan-900/50 text-cyan-400"; 
                toastIcon.className = "fa-solid fa-check text-sm"; 
                document.getElementById('toast-title-bottom').className = "text-[10px] font-orbitron font-black uppercase tracking-widest text-cyan-400 leading-tight";
            } else { 
                toastBg.className = "glass-panel p-2.5 rounded-xl flex items-center gap-3 border border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.2)]"; 
                toastIconBg.className = "w-7 h-7 rounded-full flex items-center justify-center shrink-0 bg-red-900/50 text-red-500"; 
                toastIcon.className = "fa-solid fa-xmark text-sm"; 
                document.getElementById('toast-title-bottom').className = "text-[10px] font-orbitron font-black uppercase tracking-widest text-red-500 leading-tight";
            }
            
            document.getElementById('toast-title-bottom').innerText = title; 
            document.getElementById('toast-msg-bottom').innerText = msg;
            
            toast.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none'); 
            toast.classList.add('translate-y-0', 'opacity-100');
            
            let toastTimer = setTimeout(() => { 
                toast.classList.remove('translate-y-0', 'opacity-100'); 
                toast.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none'); 
            }, 4000);
            
            toast.onclick = function() {
                clearTimeout(toastTimer);
                this.classList.remove('translate-y-0', 'opacity-100'); 
                this.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
            };
        }

        function customConfirm(msg, callback) {
            const modal = document.getElementById('modal-confirm');
            const content = document.getElementById('modal-content-confirm');
            document.getElementById('confirm-msg').innerText = msg;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => content.classList.remove('scale-95'), 10);
            
            document.getElementById('btn-confirm-cancel').onclick = () => {
                content.classList.add('scale-95'); setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 200);
            };
            document.getElementById('btn-confirm-ok').onclick = () => {
                content.classList.add('scale-95'); setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 200);
                callback();
            };
        }

        function customPrompt(oldName, callback) {
            const modal = document.getElementById('modal-prompt');
            const content = document.getElementById('modal-content-prompt');
            const input = document.getElementById('prompt-input');
            input.value = oldName;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => content.classList.remove('scale-95'), 10);
            setTimeout(() => input.focus(), 50);
            
            document.getElementById('btn-prompt-cancel').onclick = () => {
                content.classList.add('scale-95'); setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 200);
            };
            document.getElementById('btn-prompt-ok').onclick = () => {
                content.classList.add('scale-95'); setTimeout(() => modal.classList.add('opacity-0', 'pointer-events-none'), 200);
                callback(input.value);
            };
        }

        function iniciarEscaneoRed() {
            const defaultBanner = document.getElementById('banner-default'); const scanBanner = document.getElementById('banner-scan'); const term = document.getElementById('inline-terminal');
            document.getElementById('main-banner-container').style.height = "90px"; defaultBanner.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                scanBanner.classList.remove('opacity-0', 'pointer-events-none'); term.innerHTML = '<div class="text-gray-500">root@goldhen:~# scan_red_estelar</div>';
                const lines = ["[*] INICIANDO RADAR CUÁNTICO...", "[*] ESCANEANDO SUBRED LOCAL...", "[*] RASTREANDO CONSOLA PS4..."];
                let delay = 600; lines.forEach((line, index) => { setTimeout(() => { term.innerHTML += `<div>${line} <span class="cursor-blink">_</span></div>`; term.scrollTop = term.scrollHeight; }, delay * (index + 1)); });
                setTimeout(() => {
                    fetch('?action=scan_network&port=' + currentPort)
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'success') {
                            currentIP = data.ip; localStorage.setItem('sebas_ip_final_libre', currentIP); document.getElementById('ip-manual-input').value = currentIP; actualizarUI(); pingConsola(false);
                            term.innerHTML += `<div class="text-white mt-1 font-bold">[+] CONSOLA DETECTADA: ${currentIP}</div>`; term.scrollTop = term.scrollHeight;
                            setTimeout(() => { resetBanner(); mostrarNotificacion("ESCANEO EXITOSO", "Conectado a:\n" + currentIP, "fa-check"); chequearBasuraPS4Background(); }, 1500);
                        } else {
                            term.innerHTML += `<div class="text-red-500 mt-1 font-bold">[-] FALLO: NO HAY CONSOLA EN RED</div>`; term.scrollTop = term.scrollHeight;
                            setTimeout(() => { resetBanner(); mostrarNotificacion("ESCANEO FALLIDO", "Consola no encontrada.", "fa-times"); }, 2000);
                        }
                    })
                    .catch(() => { term.innerHTML += `<div class="text-red-500 mt-1 font-bold">[!] FATAL: FALLO DE RADAR LOCAL</div>`; term.scrollTop = term.scrollHeight; setTimeout(() => { resetBanner(); mostrarNotificacion("ERROR DE RED", "Fallo el radar.", "fa-times"); }, 2000); });
                }, delay * (lines.length + 1));
            }, 300);
        }

        function resetBanner() {
            document.getElementById('banner-scan').classList.add('opacity-0', 'pointer-events-none'); document.getElementById('main-banner-container').style.height = "60px";
            setTimeout(() => { document.getElementById('banner-default').classList.remove('opacity-0', 'pointer-events-none'); }, 300);
        }

        function guardarConfigManual() {
            const ipVal = document.getElementById('ip-manual-input').value.trim(); const portVal = document.getElementById('puerto-manual-input').value;
            if(ipVal !== '') {
                currentIP = ipVal; currentPort = portVal; localStorage.setItem('sebas_ip_final_libre', currentIP); localStorage.setItem('ps4_store_puerto', currentPort); actualizarUI(); pingConsola(false); toggleModal('modal-config'); mostrarNotificacion("GUARDADO", "IP Actualizada.", "fa-check");
            } else { mostrarNotificacion("ERROR", "Ingresa una IP válida.", "fa-times"); }
        }

        function alternarPuertoRPI() { currentPort = currentPort === '12801' ? '12800' : '12801'; localStorage.setItem('ps4_store_puerto', currentPort); document.getElementById('puerto-manual-input').value = currentPort; actualizarUI(); if(currentIP !== '') pingConsola(false); }

        function setDotState(state, textHead) {
            const dot = document.getElementById('ui-dot'); const txt = document.getElementById('status-txt-head'); txt.innerText = textHead;
            const radarIcon = document.getElementById('radar-icon');
            if(state === 'online') { 
                dot.className = 'w-1.5 h-1.5 rounded-full bg-[#39ff14] shadow-[0_0_10px_#39ff14]'; 
                txt.className = 'text-[9px] font-tech font-bold text-[#39ff14] uppercase tracking-widest leading-none'; 
                if(radarIcon) radarIcon.classList.add('drop-shadow-[0_0_8px_#22d3ee]', 'animate-pulse');
            } 
            else if (state === 'offline') { 
                dot.className = 'w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_10px_#f00]'; 
                txt.className = 'text-[9px] font-tech font-bold text-red-400 uppercase tracking-widest leading-none'; 
                if(radarIcon) radarIcon.classList.remove('drop-shadow-[0_0_8px_#22d3ee]', 'animate-pulse');
            } 
            else { 
                dot.className = 'w-1.5 h-1.5 rounded-full bg-yellow-400 shadow-[0_0_10px_#ff0]'; 
                txt.className = 'text-[9px] font-tech font-bold text-yellow-400 uppercase tracking-widest leading-none'; 
                if(radarIcon) radarIcon.classList.remove('drop-shadow-[0_0_8px_#22d3ee]', 'animate-pulse');
            }
        }

        function pingConsola(isManualTest = false) {
            let ipTest = currentIP;
            let portTest = currentPort;
            
            if (isManualTest) {
                ipTest = document.getElementById('ip-manual-input').value.trim();
                portTest = document.getElementById('puerto-manual-input').value;
                if(!ipTest) {
                    mostrarNotificacion("ERROR", "Escribe una IP válida.", "fa-times");
                    return;
                }
            } else {
                if(!currentIP) return;
            }

            setDotState('ping', 'PING...');
            fetch(window.location.pathname + `?check_ip=${encodeURIComponent(ipTest)}&port=${encodeURIComponent(portTest)}`)
            .then(r => r.json())
            .then(data => {
                if(data.status === 'online') { 
                    setDotState('online', 'CONECTADA'); 
                    if(isManualTest) mostrarNotificacion('ÉXITO', 'Consola Online.', "fa-check"); 
                } else { 
                    setDotState('offline', 'OFFLINE'); 
                    if(isManualTest) mostrarNotificacion('SIN RESPUESTA', 'Verifica RPI en PS4.', "fa-times"); 
                }
            }).catch(() => { 
                setDotState('offline', 'ERROR'); 
                if(isManualTest) mostrarNotificacion('ERROR', 'Fallo de red local.', "fa-times"); 
            });
        }

        function moverPkgsDesdeCelular(btnElement) {
            const textoOriginal = btnElement.innerHTML; btnElement.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i>'; btnElement.disabled = true;
            const formData = new FormData(); formData.append('action', 'move_pkgs');
            fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.success && data.movidos > 0) { mostrarNotificacion('COMPLETADO', `${data.movidos} PKGs extraídos.`, "fa-check"); setTimeout(() => location.reload(), 1500); } 
                else if(data.success && data.movidos === 0) { mostrarNotificacion('VACÍO', 'Descargas vacía.', "fa-times"); } 
                else { mostrarNotificacion('ERROR', data.message, "fa-times"); }
            })
            .catch(() => { mostrarNotificacion('ERROR', 'Fallo al procesar.', "fa-times"); })
            .finally(() => { btnElement.innerHTML = textoOriginal; btnElement.disabled = false; });
        }

        function borrarPKG(nombre) {
            customConfirm("¿Eliminar de forma PERMANENTE este archivo?\n\n" + nombre, () => {
                const formData = new FormData(); formData.append('action', 'delete_pkg'); formData.append('archivo_nombre', nombre);
                fetch(window.location.href, { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                    if(data.success) { mostrarNotificacion("BORRADO", "Archivo eliminado.", "fa-check"); setTimeout(() => location.reload(), 1200); } else { mostrarNotificacion("ERROR", data.message, "fa-times"); }
                });
            });
        }

        function renombrarPKG(nombreViejo) {
            customPrompt(nombreViejo, (nuevoNombre) => {
                if(!nuevoNombre || nuevoNombre === nombreViejo) return;
                const formData = new FormData(); formData.append('action', 'rename_pkg'); formData.append('viejo_nombre', nombreViejo); formData.append('nuevo_nombre', nuevoNombre);
                fetch(window.location.href, { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                    if(data.success) { mostrarNotificacion("RENOMBRADO", "Nombre modificado.", "fa-check"); setTimeout(() => location.reload(), 1200); } else { mostrarNotificacion("ERROR", data.message, "fa-times"); }
                });
            });
        }

        function abrirLectorPKG(elementoDiv) {
            currentFileName = elementoDiv.getAttribute('data-filename'); 
            const modal = document.getElementById('modal-preview-pkg'); const content = document.getElementById('modal-content-preview');
            modal.classList.remove('opacity-0', 'pointer-events-none'); setTimeout(() => { content.classList.remove('translate-y-full'); }, 10);
            
            document.getElementById('preview-loader').classList.remove('hidden'); document.getElementById('preview-data').classList.add('hidden'); document.getElementById('preview-img').classList.add('hidden'); document.getElementById('preview-icon-fallback').classList.remove('hidden'); document.getElementById('btn-instalar-local').classList.add('hidden');

            const formData = new FormData(); formData.append('action', 'info_pkg'); formData.append('archivo_nombre', currentFileName); 
            fetch(window.location.href, { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                currentFileBytes = data.tamano_bytes;
                document.getElementById('preview-nombre').innerText = data.nombre; document.getElementById('preview-tid').innerText = data.title_id; document.getElementById('preview-size').innerText = data.tamano_txt; document.getElementById('preview-cat').innerText = data.categoria;
                const cVer = document.getElementById('container-app-ver');
                document.getElementById('preview-ver').innerText = (!data.app_ver || data.app_ver === '---') ? '---' : data.app_ver;
                
                const coverContainer = document.getElementById('pkg-cover-container');
                coverContainer.classList.remove('holo-float', 'anim-float', 'anim-pulse', 'anim-static', 'anim-swing', 'anim-zoom', 'anim-spin');
                coverContainer.classList.add(localStorage.getItem('ps4_cover_anim') || 'holo-float');

                if(data.imagen) { document.getElementById('preview-img').src = data.imagen; document.getElementById('preview-img').classList.remove('hidden'); document.getElementById('preview-icon-fallback').classList.add('hidden'); } 
                
                const btnInstalar = document.getElementById('btn-instalar-local');
                if(currentFileBytes < 1048576) {
                    btnInstalar.innerHTML = '<i class="fa-solid fa-server text-base"></i> ENVIAR POR FTP'; btnInstalar.className = "w-full btn-green-gradient active:scale-95 text-white font-orbitron font-black text-[13px] py-4 rounded-2xl tracking-widest uppercase flex items-center justify-center gap-2 transition-transform shadow-lg mt-2"; btnInstalar.onclick = function() { instalarArchivoFTP(this); };
                } else {
                    btnInstalar.innerHTML = '<i class="fa-solid fa-paper-plane text-base"></i> ENVIAR A CONSOLA'; btnInstalar.className = "w-full btn-cyan-gradient active:scale-95 text-white font-orbitron font-black text-[14px] py-4 rounded-2xl tracking-widest uppercase flex items-center justify-center gap-2 transition-transform shadow-lg mt-2"; btnInstalar.onclick = function() { instalarArchivoLocal(this); };
                }
                btnInstalar.classList.remove('hidden'); document.getElementById('preview-loader').classList.add('hidden'); document.getElementById('preview-data').classList.remove('hidden'); document.getElementById('preview-data').classList.add('flex');
            }).catch(err => { alert("ERROR: \n" + err); cerrarPreview(); });
        }

        function cerrarPreview() {
            const modal = document.getElementById('modal-preview-pkg'); const content = document.getElementById('modal-content-preview');
            content.classList.add('translate-y-full'); setTimeout(() => { modal.classList.add('opacity-0', 'pointer-events-none'); }, 300);
        }

        function enviarEnlaceManual(btnElement) {
            const url = document.getElementById('manual-url').value.trim();
            if (!currentIP) { mostrarNotificacion("ERROR DE RED", "Configura primero la IP de la PS4.", "fa-wifi"); toggleModal('modal-config'); return; }
            if (!url) { mostrarNotificacion("ERROR", "Debes pegar un enlace directo primero.", "fa-times"); return; }

            guardarLinkHistorial(url);

            const textoOriginal = btnElement.innerHTML; 
            btnElement.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i>'; 
            btnElement.disabled = true;

            const fd = new FormData();
            fd.append('action', 'analyze_url');
            fd.append('url', url);

            fetch(window.location.href, { method: 'POST', body: fd })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    mostrarNotificacion("ERROR", data.message, "fa-times");
                    btnElement.innerHTML = textoOriginal; 
                    btnElement.disabled = false;
                    return;
                }

                const isArchive = data.type === 'archive';
                if (isArchive) {
                    btnElement.innerHTML = textoOriginal; 
                    btnElement.disabled = false;
                    document.getElementById('manual-url').value = '';
                    iniciarDescargaYExtraccion(data.direct_url, data.filename);
                } else {
                    enviarRpiDirecto([data.direct_url]);
                    document.getElementById('manual-url').value = '';
                    btnElement.innerHTML = textoOriginal; 
                    btnElement.disabled = false;
                }
            })
            .catch(err => {
                mostrarNotificacion("ERROR", "Fallo al analizar el enlace.", "fa-times");
                btnElement.innerHTML = textoOriginal; 
                btnElement.disabled = false;
            });
        }

        function iniciarDescargaYExtraccion(directUrl, filename) {
            const modalProg = document.getElementById('modal-progress');
            modalProg.classList.remove('opacity-0', 'pointer-events-none');

            const formData = new FormData();
            formData.append('action', 'download_extract');
            formData.append('direct_url', directUrl);
            formData.append('filename', filename);
            formData.append('ip', currentIP);

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                modalProg.classList.add('opacity-0', 'pointer-events-none');
                
                if (data.success) {
                    let htmlFormatted = `<div class="font-sans text-[12px] leading-relaxed">`;
                    let urlsToRpi = [];
                    let hasSmall = data.small && data.small.length > 0;
                    let hasLarge = data.large && data.large.length > 0;

                    if (hasSmall) {
                        htmlFormatted += `<div class="mb-4"><b class="text-emerald-400 font-bold block mb-1 text-[13px] uppercase tracking-widest"><i class="fa-solid fa-server mr-1"></i> ENVIADO POR FTP</b>`;
                        data.small.forEach(item => {
                            htmlFormatted += `<span class="text-gray-200 block pl-3 py-1 border-l-2 border-emerald-500/50 mb-1 bg-black/20 rounded-r-md">• ${item.name} <span class="text-emerald-300 text-[10px]">(${item.txt})</span></span>`;
                        });
                        htmlFormatted += `<span class="text-[10px] text-emerald-400/80 block pl-3 mt-1 italic">Guardados en /data/pkg/ listos para instalar.</span></div>`;
                    }

                    if (hasLarge) {
                        htmlFormatted += `<div><b class="text-cyan-400 font-bold block mb-1 text-[13px] uppercase tracking-widest"><i class="fa-solid fa-paper-plane mr-1"></i> LISTO PARA PS4 (RPI)</b>`;
                        data.large.forEach(item => {
                            htmlFormatted += `<span class="text-gray-200 block pl-3 py-1 border-l-2 border-cyan-500/50 mb-1 bg-black/20 rounded-r-md">• ${item.name} <span class="text-cyan-300 text-[10px]">(${item.txt})</span></span>`;
                            urlsToRpi.push(item.url);
                        });
                        htmlFormatted += `</div>`;
                    }
                    htmlFormatted += `</div>`;

                    let isOnlyFtp = (!hasLarge && hasSmall);
                    mostrarAlertCenter("RESULTADO DE EXTRACCIÓN", htmlFormatted, isOnlyFtp);
                    
                    if (hasLarge) { window.pendingRpiUrls = urlsToRpi; }
                } else {
                    mostrarNotificacion("ERROR", data.message || "Fallo al procesar el archivo.", "fa-times");
                }
            })
            .catch(err => {
                modalProg.classList.add('opacity-0', 'pointer-events-none');
                mostrarNotificacion("ERROR", "Fallo de conexión o archivo incompatible.", "fa-times");
            });
        }

        async function enviarRpiDirecto(urls) {
            if (!urls || urls.length === 0) return;
            
            let errores = [];
            
            for (let i = 0; i < urls.length; i++) {
                if (urls.length > 1) {
                    mostrarNotificacion("ENVIANDO A RPI", `Enviando archivo ${i+1} de ${urls.length}...`, "fa-paper-plane");
                } else {
                    mostrarNotificacion("ENVIANDO A RPI", "Iniciando envío a la consola...", "fa-paper-plane");
                }

                const formData = new FormData();
                formData.append('action', 'install_direct');
                formData.append('ip', currentIP);
                formData.append('puerto', currentPort);
                formData.append('urls', JSON.stringify([urls[i]])); 

                try {
                    let res = await fetch(window.location.href, { method: 'POST', body: formData });
                    let data = await res.json();
                    if (!data.success) {
                        errores.push(`Error (Archivo ${i+1}): ${data.message}`);
                    }
                } catch (err) {
                    errores.push(`Error (Archivo ${i+1}): Fallo de red.`);
                }
                
                if (i < urls.length - 1) {
                    await new Promise(r => setTimeout(r, 3000));
                }
            }
            
            if (errores.length === 0) {
                mostrarNotificacion("COMPLETADO", "¡Enviado a la PS4 por RPI!", "fa-check");
            } else {
                mostrarNotificacion("ERROR RPI", errores.join(" | "), "fa-times");
            }
        }

        function instalarArchivoLocal(btnElement) {
            if(!currentFileName) return;
            
            const ipCelular = "<?= $hostIp ?>";
            let puertoCelular = window.location.port || "<?= $hostPuerto ?>";
            
            const scriptPHP = "<?= $_SERVER['SCRIPT_NAME'] ?>"; 
            const linkAEnviar = `http://${ipCelular}:${puertoCelular}${scriptPHP}/${currentFileName}`;
            
            const textoOriginal = btnElement.innerHTML; 
            btnElement.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-base"></i> ENVIANDO...'; 
            btnElement.disabled = true;

            const formData = new FormData(); 
            formData.append('action', 'install_direct'); 
            formData.append('ip', currentIP); 
            formData.append('puerto', currentPort); 
            formData.append('urls', JSON.stringify([linkAEnviar])); 

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { 
                if (data.success) { mostrarNotificacion("COMPLETADO", "¡Enviado a la PS4 por RPI!", "fa-check"); } 
                else { mostrarNotificacion("ERROR RPI", data.message, "fa-times"); }
            })
            .catch(err => { mostrarNotificacion('ERROR', 'Fallo conexión.', "fa-wifi"); })
            .finally(() => { btnElement.innerHTML = textoOriginal; btnElement.disabled = false; cerrarPreview(); });
        }

        function instalarArchivoFTP(btnElement) {
            if (!currentIP) { mostrarNotificacion("ERROR DE RED", "Ingresa la IP de tu consola primero.", "fa-wifi"); toggleModal('modal-config'); return; }
            if (!currentFileName) return;

            const textoOriginal = btnElement.innerHTML; 
            btnElement.innerHTML = '<i class="fa-solid fa-server fa-bounce text-xl"></i> SUBIENDO...'; 
            btnElement.disabled = true;

            const formData = new FormData(); 
            formData.append('action', 'ftp_upload'); 
            formData.append('ip', currentIP); 
            formData.append('archivo_nombre', currentFileName); 

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => { mostrarNotificacion(data.success ? 'FTP COMPLETADO' : 'ERROR FTP', data.message, data.success ? "fa-check" : "fa-times"); })
            .catch(err => { mostrarNotificacion('ERROR CRÍTICO', 'La transferencia FTP fue abortada por la red.', "fa-exclamation-triangle"); })
            .finally(() => { btnElement.innerHTML = textoOriginal; btnElement.disabled = false; cerrarPreview(); });
        }

        // ====================================================================
        // PUENTE DE COMUNICACIÓN CON APP.JS (MÁGIA TÁCTIL)
        // ====================================================================
        let isGlobalTouchingRPI = false;
        let lastTouchXRPI = 0, lastTouchYRPI = 0;

        function dispararEfectoPadre(x, y, cantidad) {
            try {
                if (window.parent && typeof window.parent.spawnGlobalParticles === 'function') {
                    // +72px en Y para compensar el header superior de la app madre
                    window.parent.spawnGlobalParticles(x, y + 72, cantidad);
                }
            } catch(e){}
        }

        function dispararSonidoPadre() {
            try {
                if (window.parent && typeof window.parent.playSynth === 'function') {
                    window.parent.playSynth();
                }
            } catch(e){}
        }

        document.addEventListener('touchstart', (e) => {
            isGlobalTouchingRPI = true;
            lastTouchXRPI = e.touches[0].clientX;
            lastTouchYRPI = e.touches[0].clientY;
            dispararEfectoPadre(lastTouchXRPI, lastTouchYRPI, 15);
            dispararSonidoPadre();
        }, { passive: true });
        
        document.addEventListener('touchmove', (e) => {
            if (!isGlobalTouchingRPI) return;
            const cX = e.touches[0].clientX;
            const cY = e.touches[0].clientY;
            if (Math.hypot(cX - lastTouchXRPI, cY - lastTouchYRPI) > 15) { 
                dispararEfectoPadre(cX, cY, 2); 
                lastTouchXRPI = cX; 
                lastTouchYRPI = cY; 
            }
        }, { passive: true });
        
        document.addEventListener('touchend', () => isGlobalTouchingRPI = false);
        document.addEventListener('touchcancel', () => isGlobalTouchingRPI = false);
        
        document.addEventListener('mousedown', (e) => { 
            dispararEfectoPadre(e.clientX, e.clientY, 15);
            dispararSonidoPadre();
        });
    </script>
</body>
</html>
