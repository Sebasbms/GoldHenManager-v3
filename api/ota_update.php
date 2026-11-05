<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/ota_update.php
 * ====================================================================
 */
error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(300); // Otorgamos hasta 5 minutos por si la red es lenta

header('Content-Type: application/json; charset=utf-8');

// Validar petición y URL del ZIP
if (!isset($_POST['action']) || $_POST['action'] !== 'install_ota' || empty($_POST['zip_url'])) {
    echo json_encode(['status' => 'error', 'message' => 'Petición inválida o falta la URL del paquete.']);
    exit;
}

$zip_url = $_POST['zip_url'];
$temp_dir = __DIR__ . '/../temp_ota';
$zip_file = $temp_dir . '/update.zip';

// 1. Crear el entorno temporal
if (!is_dir($temp_dir)) {
    @mkdir($temp_dir, 0777, true);
}

// 2. Descargar el archivo ZIP desde GitHub usando cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $zip_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Vital para las redirecciones de GitHub
curl_setopt($ch, CURLOPT_USERAGENT, 'GoldHenManager-OTA-Updater/1.0'); // Obligatorio para evitar error 403
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$file_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || $file_data === false) {
    echo json_encode(['status' => 'error', 'message' => 'Fallo la descarga desde GitHub. HTTP Code: ' . $http_code]);
    exit;
}

// Guardar el ZIP localmente
file_put_contents($zip_file, $file_data);

// 3. Extracción y Manejo del ZIP
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    $zip->extractTo($temp_dir);
    $zip->close();
    
    // Borramos el ZIP inmediatamente para liberar espacio
    @unlink($zip_file);
    
    // 4. Ubicar la carpeta extraída (GitHub encapsula todo en "Usuario-Repo-rama")
    $directorios = glob($temp_dir . '/*', GLOB_ONLYDIR);
    if (!empty($directorios)) {
        $carpeta_base_github = $directorios[0]; 
        $carpeta_destino_raiz = __DIR__ . '/..'; // Volvemos a la raíz de GoldHenManager
        
        // Función recursiva nativa para sobreescribir la app entera respetando estructura
        function moverEstructuraRecursiva($src, $dst) {
            $dir = opendir($src);
            @mkdir($dst, 0777, true);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        moverEstructuraRecursiva($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        // Forzar el reemplazo del archivo
                        @rename($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        // Ejecutar el traslado de archivos
        moverEstructuraRecursiva($carpeta_base_github, $carpeta_destino_raiz);
        
        // 5. Limpieza de rastros
        function borrarDirectorioCompleto($dir) {
            if (!is_dir($dir)) return;
            $archivos = array_diff(scandir($dir), array('.', '..'));
            foreach ($archivos as $archivo) {
                (is_dir("$dir/$archivo")) ? borrarDirectorioCompleto("$dir/$archivo") : @unlink("$dir/$archivo");
            }
            @rmdir($dir);
        }
        
        borrarDirectorioCompleto($temp_dir);
        
        echo json_encode(['status' => 'success', 'message' => 'OTA instalada perfectamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Estructura no reconocida en el paquete ZIP.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo abrir o extraer el archivo ZIP.']);
}

// Developed By SeBaS
?>
