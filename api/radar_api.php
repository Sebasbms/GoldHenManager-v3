<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/radar_api.php
 * ====================================================================
 */
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

$local_ip = '';

if (isset($_SERVER['HTTP_HOST'])) {
    $host_parts = explode(':', $_SERVER['HTTP_HOST']);
    $local_ip = $host_parts[0];
}

if (empty($local_ip) || $local_ip === '127.0.0.1' || $local_ip === 'localhost') {
    $sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    if ($sock) {
        @socket_connect($sock, "8.8.8.8", 53);
        @socket_getsockname($sock, $local_ip);
        @socket_close($sock);
    }
}

$parts = explode('.', $local_ip);
array_pop($parts);
$base_ip = implode('.', $parts); 

if (!$base_ip || count($parts) !== 3 || $base_ip === '127.0.0') {
    echo json_encode(['status' => 'error', 'message' => 'KSWEB bloqueó la lectura de la IP de red local.']);
    exit;
}

$port = 2121;
$timeout_sec = 0;
$timeout_usec = 250000;

$sockets = [];
$active_ips = [];

for ($i = 1; $i <= 254; $i++) {
    $ip = "$base_ip.$i";
    if ($ip === $local_ip) continue; 

    $sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($sock) {
        @socket_set_nonblock($sock);
        @socket_connect($sock, $ip, $port);
        $sockets[$ip] = $sock;
    }
}

$read = $sockets;
$write = $sockets;
$except = null;

$num_changed = @socket_select($read, $write, $except, $timeout_sec, $timeout_usec);

if ($num_changed > 0) {
    foreach ($write as $sock) {
        $ip = array_search($sock, $sockets);
        $error = @socket_get_option($sock, SOL_SOCKET, SO_ERROR);
        if ($error === 0) {
            $active_ips[] = $ip;
        }
    }
}

foreach ($sockets as $sock) {
    @socket_close($sock);
}

echo json_encode([
    'status' => 'success',
    'local_ip' => $local_ip,
    'segmento' => "$base_ip.x",
    'ps4_ips' => array_values(array_unique($active_ips))
]);
// Developed By SeBaS
?>
