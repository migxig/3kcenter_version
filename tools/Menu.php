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
    private $center_url = 'http://admin-center.demo.3kwan.com:82';
    private $key = 'centerkey';
    public static $menuArr = [];

    public function __construct()
    {
        $this->getMenuList();
    }

    /**
     * 签名
     * @return string
     */
    public function getSign()
    {
        $time = time();
        $HTTP_REFERER = "admin-center.demo.3kwan.com:82/";
        $sign = md5($HTTP_REFERER . $time . $this->key);

        return $sign;
    }

    /**
     * 菜单列表
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

        //组装公共部分和前端部分
        $otherList = [
            'comment'=>[
                'id' => 'comment',
                'name' => '公共部分',
                'children' => [
                    [
                        'id' => 'Public',
                        'name' => '公共模块',
                        'children' => [
                            [
                                'id' => 'comment_Public-listCommentPublic',
                                'name' => '公共内容',
                                'ct' => 'comment_Public',
                                'hidden' => 0,
                            ],
                        ],
                    ],
                ]
            ],
            'js'=>[
                'id' => 'js',
                'name' => '前端部分',
                'children' => [
                    [
                        'id' => 'Html',
                        'name' => '前端模块',
                        'children' => [
                            [
                                'id' => 'js_Html-listHtmlJs',
                                'name' => '前端内容',
                                'ct' => 'js_Html',
                                'hidden' => 0,
                            ],
                        ],
                    ]
                ]
            ],
            'mobile'=>[
                'id' => 'mobile',
                'name' => '移动报表',
                'children' => [
                    [
                        'id' => 'Report',
                        'name' => '移动报表',
                        'children' => [
                            [
                                'id' => 'mobile_Report-listMobileReport',
                                'name' => '移动报表',
                                'ct' => 'mobile_Report',
                                'hidden' => 0,
                            ],
                        ],
                    ],
                ]
            ],
        ];
        $menuList = array_merge($menuList, $otherList);

        self::$menuArr = $menuList;
    }

    /**
     * 系统列表
     * @return array
     */
    public function getSysMenu()
    {
        if (self::$menuArr) {
            $menuList = self::$menuArr;
        } else {
            $this->getMenuList();
            $menuList = self::$menuArr;
        }

        $sysArr = [];
        foreach ($menuList as $sys=>$sysItem) {
            $sysArr[] = ['id'=>$sysItem['id'], 'name'=>$sysItem['name']];
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

    /**
     * 获取名称
     * @param $id
     * @param $type
     * @return string
     */
    public function getNameById($id, $type)
    {
        if (self::$menuArr) {
            $menuList =  self::$menuArr;
        } else {
            $this->getMenuList();
            $menuList =  self::$menuArr;
        }

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

        switch ($type) {
            case 'sys':
                $data = $sysList;
                break;
            case 'model':
                $data = $modelList;
                break;
            case 'func':
                $data = $funcList;
                break;
            default:
                $data = [];
                break;
        }

        if (isset($data[$id])) {
            return $data[$id]['name'];
        } else {
            return $id;
        }
    }
}