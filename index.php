<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/10 0010
 * Time: 4:50
 */

require_once('vendor/autoload.php');

header("Content-Type: text/html;charset=utf-8");


//自动加载
spl_autoload_register(function ($class) {
    $file = str_replace("\\", "/", $class) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});