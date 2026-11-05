<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/scanner.php
 * ====================================================================
 */
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

$ip = $_GET['ip'] ?? '';
$port = $_GET['port'] ?? 2121; 

if (empty($ip) || strpos($ip, '127.') === 0 || $ip === '0.0.0.0') { 
    echo json_encode(['status' => 'error']); 
    exit; 
}

$timeout = 1.0; 
$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);

if ($fp) {
    fclose($fp);
    echo json_encode(['status' => 'success', 'ip' => $ip]); 
} else { 
    echo json_encode(['status' => 'error']); 
}
// Developed By SeBaS
?>
