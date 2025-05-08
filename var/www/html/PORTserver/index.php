<?php
// Ejecutar acciones si vienen por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $container = escapeshellcmd($_POST['container'] ?? '');

    if (!empty($action) && !empty($container)) {
        if ($action === 'stop') {
            shell_exec("lxc stop $container");
        } elseif ($action === 'start') {
            shell_exec("lxc start $container");
            sleep(2); // Esperar que inicie
            shell_exec("lxc exec $container -- bash -c 'cd /root/1449/Linux && ./TerrariaServer.bin.x86_64 -config config.txt'");
        }
    }

    header("Location: index.php");
    exit;
}

// Cargar contenedores
$servers = [];
$files = glob(__DIR__ . '/*.data');
foreach ($files as $file) {
    $content = json_decode(file_get_contents($file), true);
    if ($content) {
        $servers[] = $content;
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servidores Terraria</title>
    <link rel="icon" href="terraria1234.png" type="image/png">
    <style>
        /* Estilo para el botón de navegación */
        .boton-navegacion {
            position: fixed;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            font-size: 28px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            z-index: 1000;
        }
        .boton-navegacion:hover {
            background-color: #0056b3;
        }

        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');

        body {
            background: url('https://i.redd.it/zajy8wh63xl51.gif') no-repeat center center fixed;
            background-size: cover;
            font-family: 'San Francisco', cursive;
            color: #0f0;
            margin: 0;
            padding: 0;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 40px 20px;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            color: #0f0;
            text-shadow: 0 0 10px #0f0;
            margin-bottom: 30px;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            box-shadow: 0 0 20px #0f0;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
            border: 2px solid #0f0;
        }

        th {
            background-color: #000;
            color: #0f0;
            font-size: 14px;
        }

        td {
            background-color: #111;
            color: #fff;
            font-size: 14px;
        }

        tr:hover td {
            background-color: #222;
            color: #0f0;
            transition: 0.3s;
        }

        .no-servers {
            color: #f00;
            font-size: 14px;
            margin-top: 30px;
        }

        .power-btn {
            font-family: 'Press Start 2P', cursive;
            padding: 10px 14px;
            margin: 10px;
            font-size: 12px;
            border: 2px solid #0f0;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 0 5px #0f0;
        }

        .start-btn {
            background-color: #002f00;
            color: #0f0;
            float: right;
        }

        .start-btn:hover {
            background-color: #004d00;
            box-shadow: 0 0 10px #0f0;
        }

        .stop-btn {
            background-color: #2f0000;
            color: #f00;
            border-color: #f00;
            box-shadow: 0 0 5px #f00;
            float: left;
        }

        .stop-btn:hover {
            background-color: #5c0000;
            box-shadow: 0 0 10px #f00;
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 8px;
            }
        }

        .btn-row {
            background-color: #000;
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #0f0;
        }

        .btn-row form {
            margin: 0 10px;
        }

	.delete-btn{
	  background-color: #2f0000;
	  color: #3944e3;
	  border-color: #faf20a;
	  border-bottom: 2px solid #faf20a;
	  box-shadow: 0 0 5px #faf20a;
	}

	a.power-btn[style*="opacity:0.5"] {
    	 cursor: not-allowed;
    	 pointer-events: none !important;
	}

    </style>
</head>

<script>
function handleAction(link) {
    link.style.pointerEvents = 'none';
    link.style.opacity = '0.5';
    link.textContent = 'Procesando...';
    return true;
}
</script>

<body>

    <!-- Botón de navegación -->
    <button class="boton-navegacion" onclick="location.href='/navegacion.html'">Tornar a la pàgina principal</button>

    <div class="overlay">
        <h1> Contenidors de Terraria actius </h1>

        <table>
            <thead>
                <tr>
                    <th>Contenidor</th>
                    <th>Món</th>
                    <th>Llavor</th>
                    <th>Jugadors</th>
                    <th>Dificultat</th>
                    <th>Port</th>
                    <th>IP</th>
                    <th>Estat</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($servers) > 0): ?>
                    <?php foreach ($servers as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['nombre']) ?></td>
                            <td><?= htmlspecialchars($s['mundo']) ?></td>
                            <td><?= htmlspecialchars($s['semilla']) ?></td>
                            <td><?= htmlspecialchars($s['jugadores']) ?></td>
                            <td>
                                <?php
                                    switch ($s['dificultad']) {
                                        case '0': echo 'Normal'; break;
                                        case '1': echo 'Experto'; break;
                                        case '2': echo 'Maestro'; break;
                                        default: echo 'Desconocida';
                                    }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($s['puerto']) ?></td>
                            <td><?= htmlspecialchars($s['ip']) ?></td>
                            <td><?= htmlspecialchars($s['estado']) ?></td>
                            <td><?= htmlspecialchars($s['fecha']) ?></td>
                            <td colspan="9">
                                <div class="btn-row">
					<a href="apagar.php?name=<?php echo htmlspecialchars($s['nombre']) ?>" class="power-btn stop-btn" onclick="return handleAction(this)">APAGAR</a>
                                        <a href="borrar.php?name=<?php echo htmlspecialchars($s['nombre']) ?>" class="power-btn delete-btn" onclick="return handleAction(this)">BORRAR</a>
                                        <a href="start.php?name=<?php echo htmlspecialchars($s['nombre']) ?>&mundo=<?php echo htmlspecialchars($s['mundo']) ?>" class="power-btn start-btn" onclick="return handleAction(this)">ENCENDER</a>
					<a href="estatus.php?name=<?php echo htmlspecialchars($s['nombre']) ?>" class="power-btn estado-btn" onclick="return handleAction(this)">STATUS</a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="no-servers">No hay servidores activos </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
