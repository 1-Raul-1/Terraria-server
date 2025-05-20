<?php
if (!isset($_GET['name']) || empty($_GET['name'])) {
    header("Location: index.php");
    exit;
}

$container_name = $_GET['name'];
$data_file = __DIR__ . '/' . $container_name . '.data';

function get_lxc_status($container_name) {
    $container_name_clean = escapeshellarg(trim($container_name));
    $lxc_info = shell_exec("sudo lxc info {$container_name_clean} 2>&1");
    $yamlConfig = yaml_parse(strval($lxc_info));
    error_log(strval($lxc_info));

   if ($yamlConfig['Status']){
     return $yamlConfig['Status'];
   } else {
     return 'Status not found';
   }
//    return $yamlConfig['Status'] ? $yamlConfig['Status'] : 'Status not found';


}

function get_lxc_ip($container_name) {
    $container_name_clean = escapeshellarg(trim($container_name));
    $ip = shell_exec("sudo lxc list {$container_name_clean} --format csv | cut -d',' -f3 | awk '{print $1}'");
    return trim($ip);
}

if (file_exists($data_file)) {

    $data_content = json_decode(file_get_contents($data_file), true);

    if ($data_content) {

        $data_content['estado'] = get_lxc_status($container_name);

        file_put_contents($data_file, json_encode($data_content, JSON_PRETTY_PRINT));
    }
}

header("Location: index.php");
?>
