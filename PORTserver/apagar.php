<?php
        $container_name = escapeshellarg($_GET["name"]);
        $comandstop = "sudo lxc stop $container_name";
	shell_exec($comandstop);
	header("Location: /PORTserver/index.php");
?>
