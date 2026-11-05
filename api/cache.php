<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/cache.php
 * ====================================================================
 */
function limpiar_cache_antigua($directorio, $dias_maximos = 30) {
    if (!is_dir($directorio)) return;
    
    $archivos = glob(rtrim($directorio, '/') . '/*');
    $tiempo_limite = time() - ($dias_maximos * 86400);
    
    if (is_array($archivos)) {
        foreach ($archivos as $archivo) {
            if (is_file($archivo) && basename($archivo) !== '.nomedia') {
                if (filemtime($archivo) < $tiempo_limite) {
                    @unlink($archivo);
                }
            }
        }
    }
}
?>
