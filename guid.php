<?php
include 'tools/GuidHelper.php';

$type = $_GET['type'];
$guid = explode(',', $_GET['val']);
if (empty($type) || empty($guid)) {
    exit('参数异常');
}

$guidHelper = new GuidHelper();
$str = '';
switch ($type) {
    case 'encode':
        foreach ($guid as $val) {
            $str .= $val.' ======> '.$guidHelper::encode($val)."<br>";
        }
        break;
    case 'decode':
        foreach ($guid as $val) {
            $str .= $val.' ======> '.$guidHelper::decode($val)."<br>";
        }
        break;
        break;
    default:
        break;
}

exit($str);