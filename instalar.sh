#!/data/data/com.termux/files/usr/bin/bash

# ==========================================
# COLORES ESTILO HACKER
# ==========================================
VERDE='\033[1;32m'
CYAN='\033[1;36m'
AMARILLO='\033[1;33m'
ROJO='\033[1;31m'
BLANCO='\033[1;37m'
NC='\033[0m'

clear
echo -e "${VERDE}  ____      _    _  _               ${NC}"
echo -e "${VERDE} / ___| ___| |__| || |___ _ __      ${NC}"
echo -e "${VERDE}| |  _ / _ \ |/ _\` | '__/ _ \ '_ \  ${NC}"
echo -e "${VERDE}| |_| |  __/ | (_| | | |  __/ | | | ${NC}"
echo -e "${VERDE} \____|\___|_|\__,_|_|  \___|_| |_| ${NC}"
echo -e "${CYAN}        M A N A G E R   V 3 . 0     ${NC}"
echo -e "${BLANCO}             [ By SeBaS ]           ${NC}\n"

# 1. Solicitar permisos a Android
echo -e "${AMARILLO}[*] Abriendo brecha en el sistema (Permisos)...${NC}"
echo -e "${ROJO}[!] ATENCIÓN: Toca 'PERMITIR' en tu pantalla.${NC}"
termux-setup-storage
sleep 4 

# 2. Instalar el motor
echo -e "\n${AMARILLO}[*] Inyectando dependencias (PHP, Git, API)...${NC}"
pkg update -y && pkg upgrade -y > /dev/null 2>&1
pkg install -y git php termux-api > /dev/null 2>&1

# 3. Crear carpeta visible en Android
echo -e "${AMARILLO}[*] Creando estructura de datos en Android...${NC}"
mkdir -p /sdcard/GoldHenManager/user

# 4. Descargar tu código desde GitHub
REPO_DIR="$HOME/GoldHenManager-v3"
if [ -d "$REPO_DIR" ]; then
    echo -e "${AMARILLO}[*] Sincronizando con el servidor de SeBaS...${NC}"
    cd "$REPO_DIR" && git pull > /dev/null 2>&1
else
    echo -e "${AMARILLO}[*] Clonando repositorio maestro...${NC}"
    git clone https://github.com/Sebasbms/GoldHenManager-v3.git "$REPO_DIR" > /dev/null 2>&1
fi

# 5. Crear el puente mágico (Enlace Simbólico)
echo -e "${AMARILLO}[*] Estableciendo túnel de archivos (Symlink)...${NC}"
rm -rf "$REPO_DIR/user" 2>/dev/null
ln -s /sdcard/GoldHenManager/user "$REPO_DIR/user"

# 6. Grabar el script de arranque visual y servidor en Termux
echo -e "${AMARILLO}[*] Sobrescribiendo terminal de arranque...${NC}"
cat << 'EOF' > $HOME/.bashrc
VERDE='\033[1;32m'
CYAN='\033[1;36m'
AMARILLO='\033[1;33m'
BLANCO='\033[1;37m'
NC='\033[0m'

# Función para redibujar el logo (Efecto animación)
imprimir_logo() {
    clear
    echo -e "${VERDE}  ____      _    _  _               ${NC}"
    echo -e "${VERDE} / ___| ___| |__| || |___ _ __      ${NC}"
    echo -e "${VERDE}| |  _ / _ \ |/ _\` | '__/ _ \ '_ \  ${NC}"
    echo -e "${VERDE}| |_| |  __/ | (_| | | |  __/ | | | ${NC}"
    echo -e "${VERDE} \____|\___|_|\__,_|_|  \___|_| |_| ${NC}"
    echo -e "${CYAN}        M A N A G E R   V 3 . 0     ${NC}"
    echo -e "${BLANCO}             [ By SeBaS ]           ${NC}\n"
}

# Matar procesos de servidor anteriores
pkill -f "php -S" > /dev/null 2>&1

APP_DIR="$HOME/GoldHenManager-v3"
PUERTO=8080

if [ -d "$APP_DIR" ]; then
    cd "$APP_DIR"
    
    # Arrancamos PHP silenciosamente de fondo
    PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:${PUERTO} > /dev/null 2>&1 &
    
    # --- ANIMACIÓN DE CUENTA REGRESIVA CON NÚMEROS GIGANTES ---
    
    # Número 3
    imprimir_logo
    echo -e "${AMARILLO} [+] Conexión establecida. Iniciando entorno...${NC}\n"
    echo -e "${CYAN}           ████████   ${NC}"
    echo -e "${CYAN}                 ██   ${NC}"
    echo -e "${CYAN}           ████████   ${NC}"
    echo -e "${CYAN}                 ██   ${NC}"
    echo -e "${CYAN}           ████████   ${NC}\n"
    sleep 1

    # Número 2
    imprimir_logo
    echo -e "${AMARILLO} [+] Conexión establecida. Iniciando entorno...${NC}\n"
    echo -e "${CYAN}           ████████   ${NC}"
    echo -e "${CYAN}                 ██   ${NC}"
    echo -e "${CYAN}           ████████   ${NC}"
    echo -e "${CYAN}           ██         ${NC}"
    echo -e "${CYAN}           ████████   ${NC}\n"
    sleep 1

    # Número 1
    imprimir_logo
    echo -e "${AMARILLO} [+] Conexión establecida. Iniciando entorno...${NC}\n"
    echo -e "${CYAN}             ████     ${NC}"
    echo -e "${CYAN}           ██  ██     ${NC}"
    echo -e "${CYAN}               ██     ${NC}"
    echo -e "${CYAN}               ██     ${NC}"
    echo -e "${CYAN}             ██████   ${NC}\n"
    sleep 1
    
    imprimir_logo
    echo -e "${VERDE} [+] ¡SISTEMA EN LÍNEA! Ejecutando interfaz...${NC}\n"
    echo -e "${CYAN} [i] Escribe 'exit' para apagar el servidor.${NC}\n"
    
    # Forzar al teléfono a abrir el navegador
    termux-open-url "http://localhost:${PUERTO}"
fi
EOF

# 7. Finalizar
echo -e "\n${VERDE}=================================================${NC}"
echo -e "${VERDE}  ¡INSTALACIÓN COMPLETADA!                       ${NC}"
echo -e "${VERDE}=================================================${NC}"
echo -e "${CYAN}Cierra Termux por completo y vuélvelo a abrir para ver la magia.${NC}"
