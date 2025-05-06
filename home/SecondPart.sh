#!/bin/bash
# Cargar variables de configuración
if [ -f "/root/config.txt" ]; then
   source /root/config.txt
else
   echo "Error: No se encontró config.txt"
   exit 1
fi

# Ruta de descarga y ejecución del servidor
URL_DOWNLOAD="https://terraria.org/api/download/pc-dedicated-server/terraria-server-1449.zip"
CARPETA="terraria-server-1449"
EJECUTAR="TerrariaServer.bin.x86_64"
INSTALL_DIR="/root/1449/Linux/"

# Instalar dependencias
apt update && apt install -y wget unzip

# Descargar y extraer el servidor
wget -O "$CARPETA.zip" "$URL_DOWNLOAD"
unzip -o "$CARPETA.zip"

# Crear archivo de configuración
CONFIG_FILE="${INSTALL_DIR}config.txt"

cat > "$CONFIG_FILE" <<EOF
world=${INSTALL_DIR}Worlds/${TEST_WORLD}.wld
autocreate=1
seed=$TEST_SEED
worldname=$TEST_WORLD
difficulty=$TEST_DIFFICULTY
maxplayers=$TEST_PLAYERS
port=$TEST_PORT
password=$TEST_PASSWORD
banlist=banlist.txt
secure=1
EOF

sudo chmod 777 "$CONFIG_FILE"

# Asegurar permisos y ejecutar el servidor
chmod +x "$INSTALL_DIR/$EJECUTAR"
cd "$INSTALL_DIR"
./"$EJECUTAR" -config config.txt

