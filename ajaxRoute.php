<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-03-08
 * Time: 17:20
 */

//自动加载
spl_autoload_register(function ($class) {
    $file = str_replace("\\", "/", $class) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

$ct = !empty($_REQUEST['ct']) ? trim($_REQUEST['ct']) : '';
$ac = !empty($_REQUEST['ac']) ? trim($_REQUEST['ac']) : '';
$params = !empty($_REQUEST['params']) ? $_REQUEST['params'] : '';

if ($ct && $ac) {
    $class = "tools\\" . $ct;
    $data = (new $class)->$ac($params);
    echo json_encode($data);
}