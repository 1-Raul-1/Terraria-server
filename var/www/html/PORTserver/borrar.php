<?php
        $container_name = escapeshellarg($_GET["name"]);
        $comandstart = "sudo rm $container_name.data & sudo rm $container_name.txt";
	$comantstopcontainer = "lxc delete --force $container_name";
	$info = "bash borrar.sh $container_name";

	shell_exec($comandstart);
	shell_exec($info);

        header("Location: /PORTserver/index.php");
?>
