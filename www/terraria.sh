#!/bin/bash
# Variables
cd /home

CONTAINER_NAME="TerrariaServer$RANDOM"
IMAGE="ubuntu:24.04"
URL_DOWNLOAD="https://terraria.org/api/download/pc-dedicated-server/terraria-server-1449.zip"
SecondPart="/home/SecondPart.sh"

# Variables del servidor
TEST_SEED="12345"
TEST_WORLD="MiMundo"
TEST_DIFFICULTY="2"
TEST_PLAYERS="8"
TEST_PASSWORD=""
TEST_PORT=$(( 7000 + RANDOM % 3000 ))

# Capturar flags del script
TEMP=$(getopt -n "$0" -a -l "contenedor:,semilla:,mundo:,dificultad:,jugadores:,contraseña:" -- -- "$@")
[ $? -eq 0 ] || exit
eval set -- "$TEMP"

while [ $# -gt 0 ]; do
  case "$1" in
      --contenedor) CONTAINER_NAME="$2"; shift;;
      --semilla) TEST_SEED="$2"; shift;;
      --mundo) TEST_WORLD="$2"; shift;;
      --dificultad) TEST_DIFFICULTY="$2"; shift;;
      --jugadores) TEST_PLAYERS="$2"; shift;;
      --contraseña) TEST_PASSWORD="$2"; shift;;
      --) shift;;
  esac
  shift
done

echo $TEST_PORT >> /var/www/html/PORTserver/$CONTAINER_NAME.txt

# Verifica si el contenedor ya existe
if ! lxc list | grep -q "$CONTAINER_NAME"; then
  echo "Creando el contenedor $CONTAINER_NAME..."
  lxc launch $IMAGE $CONTAINER_NAME
  sleep 5
  lxc config device add $CONTAINER_NAME TerrariaProxy proxy connect=tcp:127.0.0.1:7777 listen=tcp:0.0.0.0:$TEST_PORT
else
  echo "El contenedor $CONTAINER_NAME ya existe."
fi

# Crear archivo de configuración en /home
cat <<EOF > /home/config.txt
TEST_SEED="$TEST_SEED"
TEST_WORLD="$TEST_WORLD"
TEST_DIFFICULTY="$TEST_DIFFICULTY"
TEST_PLAYERS="$TEST_PLAYERS"
TEST_PASSWORD="$TEST_PASSWORD"
EOF

cat <<EOF > /home/ejecutarsegundaparte.txt
ContainerName="$CONTAINER_NAME"
EOF

# Enviar archivos al contenedor
lxc file push /home/SecondPart.sh $CONTAINER_NAME/root/
lxc file push /home/config.txt $CONTAINER_NAME/root/

# Ejecutar la segunda parte dentro del contenedor
lxc exec $CONTAINER_NAME -- chmod +x /root/SecondPart.sh

tmux new-session -d -s $TEST_WORLD lxc exec $CONTAINER_NAME -- bash /root/SecondPart.sh -d

