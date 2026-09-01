#!/bin/bash
# ====================================================================
# GOLDHEN MANAGER V3.0 - ACTUALIZADOR ULTRA RÁPIDO (GIT DELTAS)
# ====================================================================

VERDE='\033[1;32m'
CYAN='\033[1;36m'
ROJO='\033[1;31m'
NC='\033[0m'

echo -e "${CYAN}========================================${NC}"
echo -e "${CYAN}  Buscando actualizaciones de SeBaS...  ${NC}"
echo -e "${CYAN}========================================${NC}"

# 1. Aseguramos que Termux tenga Git instalado
pkg install git -y > /dev/null 2>&1

# 2. Ruta de tu app (Cambia esto por la ruta real donde instalas la app)
APP_DIR="/sdcard/htdocs"

if [ ! -d "$APP_DIR" ]; then
    echo -e "${ROJO}Error: No se encontró la instalación en $APP_DIR${NC}"
    exit 1
fi

cd "$APP_DIR" || exit

# 3. La Magia: Solo descargar lo que cambió
if [ ! -d ".git" ]; then
    echo -e "Vinculando la aplicación con GitHub por primera vez..."
    # Inicializa el motor de comparación
    git init > /dev/null 2>&1
    git remote add origin https://github.com/Sebasbms/GoldHenManager-v3.git
    echo -e "Descargando estructura base..."
    git fetch origin > /dev/null 2>&1
    git reset --hard origin/main > /dev/null 2>&1
else
    echo -e "Comparando archivos locales con el servidor..."
    # ESTO ES LO QUE QUERÍAS: Solo descarga los KB/MB que hayas modificado
    git fetch origin main > /dev/null 2>&1
    git reset --hard origin/main > /dev/null 2>&1
fi

# 4. Dar permisos por si acaso
chmod -R 777 "$APP_DIR"

echo -e "${CYAN}========================================${NC}"
echo -e "${VERDE}  ¡Actualización completada en segundos! ${NC}"
echo -e "${CYAN}========================================${NC}"
