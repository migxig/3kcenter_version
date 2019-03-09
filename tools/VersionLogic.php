<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/10 0010
 * Time: 3:08
 */

namespace tools;


use SimpleDB\DB;

class VersionLogic extends Basic
{

    public function addVersion($params)
    {
        if (empty($params['version'])) {
            return ['code'=>1, 'msg'=>'请输入版本号'];
        }
        if (empty($params['user'])) {
            return ['code'=>1, 'msg'=>'请输入用户名'];
        }

        $menu = new Menu();
        $db = $this->getDb();
        $version = trim($params['version']);
        $user = trim($params['user']);
        $groups = $params['groups'];

        $code = 0;
        if ($groups) {
            foreach ($groups as $index=>$item) {
                $num = 0;
                foreach ($item as $key=>$val) {
                    if (empty($val)) {
                        ++$num;
                    }
                }
                //过滤某组空值
                if ($num == count($item)) {
                    continue;
                }

                $tmp['sys'] = !empty(Menu::$sysArr[$item['sys']]) ? Menu::$sysArr[$item['sys']]['name'] : $tmp['sys'];
                $tmp['model'] = !empty(Menu::$modelArr[$item['model']]) ? Menu::$modelArr[$item['model']]['name'] : $tmp['model'];
                $tmp['func'] = !empty(Menu::$funcArr[$item['func']]) ? Menu::$funcArr[$item['func']]['name'] : $tmp['func'];
                $tmp['content'] = trim($item['content']);
                $tmp['sql'] = trim($item['sql']);

                $tmp['version'] = $version;
                $tmp['user'] = $user;

                $res = $db->insert('version', $tmp);
                if (!$res) {
                    $code = 1;
                }
            }
        }

        if ($code === 0) {
            $msg = '添加成功';
        } else {
            $msg = '添加失败';
        }

        return ['code'=>$code, 'msg'=>$msg];
    }
}