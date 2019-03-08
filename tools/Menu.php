<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-03-08
 * Time: 15:52
 */

namespace tools;

class Menu
{
//    private $center_url = 'http://admin-center.demo.3kwan.com:82';
    private $center_url = 'https://3kadmin-center.3k.com';
    private $key = 'centerkey';

    public static $menuArr = [];
    public static $sysArr = [];
    public static $funcArr = [];

    /**
     * 获取签名
     * @return string
     */
    public function getSign()
    {
        $time = time();
        //$HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
        $HTTP_REFERER = "";
        $sign = md5($HTTP_REFERER . $time . $this->key);

        return $sign;
    }

    /**
     * 获取菜单列表
     * @return string
     */
    public function getMenuList()
    {
        if (!empty(self::$menuArr)) {
            return self::$menuArr;
        }

        $sign = $this->getSign();
        $params = [
            'ct' => 'Index',
            'ac' => 'menu',
            'sign' => $sign,
            'time' => time(),
        ];

        $func = new Func();
        $menuList = $func->http_post($this->center_url, $params);
        self::$menuArr = $menuList;

        return $menuList;
    }

    /**
     * 获取系统列表
     * @return array
     */
    public function getSysMenu()
    {
        $sysArr = [];
        if (self::$menuArr) {
            $menuList = self::$menuArr;
        } else {
            $menuList = $this->getMenuList();
        }
        foreach ($menuList as $sys=>$sysItem) {
            $sysArr[$sysItem['id']] = ['id' => $sysItem['id'], 'name' => $sysItem['name']];
        }

        return $sysArr;
    }

    /**
     * 模块列表
     * @param $params
     * @return array
     */
    public function getModelMenu($params)
    {
        if(!empty(self::$modelArr)) {
            return self::$modelArr;
        }

        if (isset($params['sys'])) {
            $sys = trim($params['sys']);
        } else {
            return [];
        }

        $modelArr = [];
        if (self::$menuArr) {
            $menuList = self::$menuArr;
        } else {
            $menuList = $this->getMenuList();
        }
        foreach ($menuList as $skey=>$sysItem) {
            if ($skey != $sys) {
                continue;
            }

            if (!empty($sysItem['children'])) {
                foreach ($sysItem['children'] as $modelItem) {
                    $modelArr[$sys.'_'.$modelItem['id']] = ['id' => $sys.'_'.$modelItem['id'], 'name' => $modelItem['name'], 'children' => $modelItem['children']];
                }
            }
        }

        return $modelArr;
    }

    /**
     * 功能列表
     * @param $params
     * @return array
     */
    public function getFuncMenu($params)
    {
        if(!empty(self::$funcArr)) {
            return self::$funcArr;
        }

        $modelArr = $this->getModelMenu($params);
        if (isset($params['model'])) {
            $model = trim($params['model']);
        } else {
            return [];
        }

        if (empty($modelArr)) {
            return [];
        }

        $funcArr = [];
        if (!empty($modelArr)) {
            foreach ($modelArr as $funcItem) {
                if (!empty($funcItem['children'])) {
                    foreach ($funcItem['children'] as $func) {
                        if($func['ct'] == $model && $func['hidden'] == 0) {
                            $funcArr[$func['id']] = ['id' => $func['id'], 'name' => $func['name']];
                        }
                    }
                }
            }
        }

        self::$funcArr = $funcArr;
        return $funcArr;
    }
}