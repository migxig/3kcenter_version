<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/10 0010
 * Time: 3:48
 */

namespace tools;

use SimpleDB\DB;

class Basic
{
    public function getDb()
    {
        $db_config = [
            'dsn' => 'mysql:host=192.168.0.231;port=25686;dbname=admin_center',
            'username' => 'ylmf',
            'password' => 'dgMDbkpGh3t',
            'charset' => 'utf8',
        ];

        $db = new DB();
        $db->setup($db_config);

        return $db;
    }

    public function getRedis()
    {

    }
}