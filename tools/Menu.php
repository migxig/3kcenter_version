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
    public static $modelArr = [];
    public static $funcArr = [];

    public function __construct()
    {
        $this->getMenuList();
    }

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
        if (self::$menuArr) {
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
        $menuList = [
            'sys'=>[
                'id'=>'sys',
                'name'=>'基础管理系统',
                'children'=>[
                    [
                        'id'=>'Base',
                        'name'=>'基础管理',
                        'children'=>[
                            [
                                'id'=>'sys_Base-listDepartment',
                                'name'=>'部门管理',
                                'hidden'=>0,
                                'ct'=>'sys_Base',
                            ],
                            [
                                'id'=>'sys_Base-listDeptGroup',
                                'name'=>'小组管理',
                                'hidden'=>0,
                                'ct'=>'sys_Base',
                            ],
                        ],
                    ],
                    [
                        'id'=>'Project',
                        'name'=>'项目管理',
                        'children'=>[
                            [
                                'id'=>'sys_Base-listProject',
                                'name'=>'项目列表',
                                'hidden'=>0,
                                'ct'=>'sys_Project'
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $sysList = [];
        $modelList = [];
        $funcList = [];
        foreach ($menuList as $sys=>$sysItem) {
            $sysList[$sysItem['id']] = ['id' => $sysItem['id'], 'name' => $sysItem['name']];
            if (!empty($sysItem['children'])) {
                foreach ($sysItem['children'] as $modelItem) {
                    $modelList[$sys.'_'.$modelItem['id']] = ['id' => $sys.'_'.$modelItem['id'], 'name' => $modelItem['name']];
                    if (!empty($modelItem['children'])) {
                        foreach ($modelItem['children'] as $funcItem) {
                            $funcList[$funcItem['id']] = ['id' => $funcItem['id'], 'name' => $funcItem['name']];
                        }
                    }
                }
            }
        }
        self::$sysArr = $sysList;
        self::$modelArr = $modelList;
        self::$funcArr = $funcList;
        self::$menuArr = $menuList;
    }

    /**
     * 获取系统列表
     * @return array
     */
    public function getSysMenu()
    {
        if (self::$sysArr) {
            return self::$sysArr;
        } else {
            $this->getMenuList();
            return self::$sysArr;
        }
    }

    /**
     * 模块列表
     * @param $params
     * @return array
     */
    public function getModelMenu($params)
    {
        if (isset($params['sys'])) {
            $sys = trim($params['sys']);
        } else {
            return [];
        }

        $modelArr = [];
        if (self::$menuArr) {
            $menuList = self::$menuArr;
        } else {
            $this->getMenuList();
            $menuList = self::$menuArr;
        }
        foreach ($menuList as $skey=>$sysItem) {
            if ($skey != $sys) {
                continue;
            }
            if (!empty($sysItem['children'])) {
                foreach ($sysItem['children'] as $modelItem) {
                    $modelArr[] = ['id' => $sys.'_'.$modelItem['id'], 'name' => $modelItem['name']];
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
        if (isset($params['model'])) {
            $model = trim($params['model']);
        } else {
            return [];
        }

        if (self::$menuArr) {
            $menuList = self::$menuArr;
        } else {
            $this->getMenuList();
            $menuList = self::$menuArr;
        }

        $funcArr = [];
        foreach ($menuList as $sys=>$sysItem) {
            if (!empty($sysItem['children'])) {
                foreach ($sysItem['children'] as $modelItem) {
                    if (!empty($modelItem['children'])) {
                        foreach ($modelItem['children'] as $funcItem) {
                            if ($funcItem['ct'] == $model && $funcItem['hidden'] == 0) {
                                $funcArr[] = ['id' => $funcItem['id'], 'name' => $funcItem['name']];
                            }
                        }
                    }
                }
            }
        }

        return $funcArr;
    }
}