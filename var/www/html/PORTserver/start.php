<?php
        $container_name = escapeshellarg($_GET["name"]);
	$world_name = escapeshellarg($_GET["mundo"]);
        $comandstart = "sudo /bin/bash encender.sh $container_name";
        shell_exec($comandstart);
        header("Location: /PORTserver/index.php");
?>
