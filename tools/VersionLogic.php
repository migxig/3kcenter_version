<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/10 0010
 * Time: 3:08
 */

namespace tools;

class VersionLogic extends Basic
{
    public function listVersion($params)
    {
        $where = ' 1 ';
        if(!empty($params['version'])) {
            $where .= " AND `version` = '{$params['version']}'";
        }
        if(!empty($params['user'])) {
            $where .= " AND `user` LIKE '{$params['user']}'";
        }

        $db = $this->getDb();
        $sql = 'SELECT * FROM `version` WHERE ' . $where . 'ORDER BY `id` DESC';
        $list = $db->fetchAll($sql);
        $list = $this->getListName($list);

        return $list;
    }

    public function getListName($list)
    {
        if ($list) {
            foreach ($list as &$item) {
                $item['time'] = date('Y-m-d H:i:s', $item['time']);
            }
            unset($item);
        }

        return $list;
    }

    /**
     * 归纳格式
     * @param $params
     * @return array
     */
    public function induceVersion($params) {
        if(empty($params['version'])) {
            return ['code'=>1, 'msg'=>'请先输入版本号', 'data'=>[], 'sql'=>[]];
        }

        $list = $this->listVersion($params);
        $data = [];
        $sql = [];
        foreach ($list as $index=>$item) {
            if (!empty($item['content'])) {
                $data[$item['sys']][] = $item['model'] . '_' .$item['func'] . '：' . $item['content'] . '；<br/>';
            }
            if (!empty($item['sql'])) {
                $sql[] = $item['sql'] . '；<br/>';
            }
        }

        if (empty($data)) {
            return ['code'=>1, 'msg'=>'该版本号无归纳内容', 'data'=>[], 'sql'=>[]];
        } else {
            return ['code'=>0, 'msg'=>'', 'data'=>$data, 'sql'=>$sql];
        }
    }

    /**
     * 新增
     * @param $params
     * @return array
     */
    public function addVersion($params)
    {
        if (empty($params['version'])) {
            return ['code'=>1, 'msg'=>'请输入版本号'];
        }
        if (empty($params['user'])) {
            return ['code'=>1, 'msg'=>'请输入用户名'];
        }

        $db = $this->getDb();
        $version = trim($params['version']);
        $user = trim($params['user']);
        $groups = $params['groups'];

        $code = 0;
        if ($groups) {
            $menu = new Menu();
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

                $tmp['sys'] = $menu->getNameById($item['sys'], 'sys');
                $tmp['model'] = $menu->getNameById($item['model'], 'model');
                $tmp['func'] = $menu->getNameById($item['func'], 'func');
                $tmp['content'] = trim($item['content']);
                $tmp['sql'] = trim($item['sql']);

                $tmp['version'] = $version;
                $tmp['user'] = $user;
                $tmp['time'] = time();

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

    public function delete($params) {
        $id = !empty($params['id']) ? intval($params['id']) : 0;
        if ($id > 0) {
            $db = $this->getDb();
            $sql = "DELETE FROM `version` WHERE `id` = " . $id;
            $res = $db->query($sql);
            if ($res) {
                return ['code'=>0, 'msg'=>'删除成功'];
            }
        } else {
            return ['code'=>1, 'msg'=>'删除失败'];
        }
    }
}