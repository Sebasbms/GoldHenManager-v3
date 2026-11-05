<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/limpiar_cache.php
 * ====================================================================
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'limpiar_user') {
    
    $user_dir = __DIR__ . '/../user';
    
    if (!is_dir($user_dir)) {
        echo json_encode(["status" => "error", "message" => "Directorio user no encontrado."]);
        exit;
    }

    function vaciarCarpetaRecursiva($carpeta) {
        $count = 0;
        if (!is_dir($carpeta)) return $count;
        
        $archivos = array_diff(scandir($carpeta), array('.', '..'));
        
        foreach ($archivos as $archivo) {
            $ruta = $carpeta . DIRECTORY_SEPARATOR . $archivo;
            @chmod($ruta, 0777); 
            
            if (is_dir($ruta)) {
                $count += vaciarCarpetaRecursiva($ruta);
                if (@rmdir($ruta)) $count++;
            } else {
                if (@unlink($ruta)) $count++;
            }
        }
        return $count;
    }

    $archivos_borrados = vaciarCarpetaRecursiva($user_dir);

    $carpetas_base = ['cache', 'portadas_custom', 'pkgs_rpi'];
    foreach ($carpetas_base as $cb) {
        $ruta_base = $user_dir . DIRECTORY_SEPARATOR . $cb;
        if (!is_dir($ruta_base)) {
            @mkdir($ruta_base, 0777, true);
        }
    }

    echo json_encode([
        "status" => "success", 
        "message" => "Limpieza profunda completada.", 
        "deleted_files" => $archivos_borrados
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Acción inválida."]);
}
?>
