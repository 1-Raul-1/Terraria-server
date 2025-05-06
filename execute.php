<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container_name = escapeshellarg($_POST["container_name"]);
    $seed = escapeshellarg($_POST["seed"]);
    $world_name = escapeshellarg($_POST["world_name"]);
    $players = escapeshellarg($_POST["players"]);
    $password = escapeshellarg($_POST["password"]);
    $difficulty = escapeshellarg($_POST["difficulty"]);

    // Comando para ejecutar el script en bash
    $command = "sudo /bin/bash /var/www/terraria.sh --contenedor=$container_name --semilla=$seed --mundo=$world_name --dificultad=$difficulty --jugadores=$players --contraseña=$password";

    // Ejecutar el script y capturar salida
    $output = shell_exec($command);

    // Hace que se lea el puerto de la carpeta de PORTserver
    $commandPuerto = "cat /var/www/html/PORTserver/$container_name.txt";
    $pureto = shell_exec($commandPuerto);
    $status = "encendido";
        // Guardar información del servidor en un archivo dentro de PORTserver
    $data = [
        "nombre" => trim($container_name, "'"),
        "semilla" => trim($seed, "'"),
        "mundo" => trim($world_name, "'"),
        "jugadores" => trim($players, "'"),
        "dificultad" => trim($difficulty, "'"),
        "puerto" => trim($pureto),
        "ip" => "192.168.238.225",
        "estado" => $status,
        "fecha" => date("Y-m-d H:i:s"),
    ];

    $file_path = "/var/www/html/PORTserver/" . $data["nombre"] . ".data";
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));

    // Verificar si el contenedor ya existe
    if (strpos($output, "El contenedor $container_name ya existe.") !== false) {
        $status = "Servidor ya existente";
    } elseif (strpos($output, "Creando el contenedor") !== false) {
        $status = "Servidor creado correctamente";
    } else {
        $status = "Error al crear el servidor";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado del Servidor</title>
    <link rel="icon" href="terraria1234.png" type="image/png">
   <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

        body {
            background: url('https://i.redd.it/zajy8wh63xl51.gif') no-repeat center center fixed;
            background-size: cover;
            font-family: 'San Francisco', cursive;
            text-align: center;
            color: #fff;
        }

        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 50px;
        }

        .status {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .info {
            text-align: left;
            margin-left: auto;
            margin-right: auto;
            width: 85%;
            background: #222;
            padding: 10px;
            border: 2px solid #0f5;
            border-radius: 5px;
        }

        button {
            font-family: 'San Francisco', cursive;
            font-size: 14px;
            margin-top: 15px;
            padding: 8px;
            border: 2px solid #0f5;
            border-radius: 5px;
            background: #0a5;
            color: #fff;
            cursor: pointer;
        }

        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Estado del Servidor</h2>

        <p class="status <?php echo ($status == 'Error al crear el servidor') ? 'error' : 'success'; ?>">
            <?php echo $status; ?>
        </p>

        <?php if ($status !== "Error al crear el servidor") { ?>
            <div class="info">
                <p><b>Nombre del Contenedor:</b> <?php echo $container_name; ?></p>
                <p><b>Semilla del Mundo:</b> <?php echo $seed; ?></p>
                <p><b>Nombre del Mundo:</b> <?php echo $world_name; ?></p>
                <p><b>Numero de Jugadores:</b> <?php echo $players; ?></p>
                <p><b>Dificultad:</b> <?php echo ($difficulty == 0) ? "Normal" : (($difficulty == 1) ? "Experto" : "Maestro"); ?></p>
                <p><b>Puerto:</b> <?php echo $pureto; ?></p>
		<p><b>Ip Del Servidor:</b> <?php echo "192.168.238.225"; ?></p>
		<p><b>Status:</b> <?php echo $status; ?></p>
            </div>
        <?php } ?>

        <br>
        <button onclick="window.location.href='/PORTserver/index.php'">Volver</button>
    </div>

</body>
</html>
