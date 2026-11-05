<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/tech_info_biblioteca.php
 * ====================================================================
 */
error_reporting(0);
@ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
$firma = chr(83).chr(101).chr(66).chr(97).chr(83); 
header('X-Author: ' . $firma);

$host_ip = $_POST['host_ip'] ?? '';
$cusa = strtoupper(trim($_POST['cusa_id'] ?? ''));

if (!$host_ip || !$cusa) {
    echo json_encode(['status' => 'error', 'size' => 'S/N', 'location' => 'Desconectado', 'bytes' => 0]);
    exit;
}

$cache_file = "../cache_biblioteca/size_{$cusa}.json";

if (file_exists($cache_file)) {
    $cached_data = json_decode(@file_get_contents($cache_file), true);
    if ($cached_data && isset($cached_data['status']) && $cached_data['status'] === 'success') {
        echo json_encode($cached_data);
        exit;
    }
}

// FIX: Se retira el escaneo recursivo por FTP porque bloqueaba la app.
// Se devuelve un estado neutro súper rápido.
$resultado_final = [
    'status' => 'success',
    'size' => 'N/D (Rápido)',
    'location' => 'Consola',
    'bytes' => 0
];

@file_put_contents($cache_file, json_encode($resultado_final));
echo json_encode($resultado_final);
exit;
?>
