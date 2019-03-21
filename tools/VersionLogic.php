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

        //分页
        $page_no = !empty($params['page_no']) ? $params['page_no'] : 1;
        $page_size = !empty($params['page_size']) ? $params['page_size'] : 10;
        $offset = ($page_no - 1) * $page_size;

        $db = $this->getDb();
        $sql = "SELECT * FROM `version` WHERE {$where} ORDER BY `id` DESC LIMIT {$offset},{$page_size}" ;
        $list = $db->fetchAll($sql);
        $list = $this->getListName($list);

        $countSql = "SELECT COUNT(*) count FROM `version` WHERE {$where}";
        $countRow = $db->fetch($countSql);

        return ['count' => intval($countRow['count']), 'list' => $list];
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

        $where = ' 1 ';
        if(!empty($params['version'])) {
            $where .= " AND `version` = '{$params['version']}'";
        }
        if(!empty($params['user'])) {
            $where .= " AND `user` LIKE '{$params['user']}'";
        }

        $sql = "SELECT * FROM `version` WHERE {$where} ORDER BY `id` DESC" ;
        $db = $this->getDb();
        $list = $db->fetchAll($sql);
        $list = $this->getListName($list);

        $data = [];
        $sql = [];
        $commentKey = '公共部分';
        $commentArr = [];
        $jsKey = '前端部分';
        $jsArr = [];
        foreach ($list as $index=>$item) {
            if (!empty($item['content'])) {
                //公共部分+前端部分 放在最后
                if ($item['sys'] == $commentKey) {
                    $commentArr[] = $item['model'] . '_' .$item['func'] . '：' . $item['content'] . '<br/>';
                } else if ($item['sys'] == $jsKey) {
                    $jsArr[] = $item['model'] . '_' .$item['func'] . '：' . $item['content'] . '<br/>';
                } else {
                    $data[$item['sys']][] = $item['model'] . '_' .$item['func'] . '：' . $item['content'] . '<br/>';
                }
            }
            if (!empty($item['sql'])) {
                $sql[] = $item['sql'] . '<br/>';
            }
        }

        if (!empty($commentArr)) {
            $data[$commentKey] = $commentArr;
        }
        if (!empty($jsArr)) {
            $data[$jsKey] = $jsArr;
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
                $tmp['remark'] = trim($item['remark']);

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

    /**
     * 编辑
     * @param $params
     * @return array
     */
    public function editVersion($params)
    {
        $id = !empty($params['id']) ? intval($params['id']) : 0;
        if (!$id) {
            return ['code'=>1, 'msg'=>'ID参数异常'];
        }
        $where['id'] = $id;

        $data = [];
        isset($params['content']) && $data['content'] = trim($params['content']);
        isset($params['sql']) && $data['sql'] = trim($params['sql']);
        isset($params['remark']) && $data['remark'] = trim($params['remark']);

        $db = $this->getDb();
        $res = $db->update('version', $data, $where);
        if ($res) {
            return ['code'=>0, 'msg'=>'编辑成功'];
        } else {
            return ['code'=>1, 'msg'=>'编辑失败'];
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getRowById($params)
    {
        $id = !empty($params['id']) ? intval($params['id']) : 0;
        if ($id > 0) {
            $db = $this->getDb();
            $sql = "SELECT * FROM `version` WHERE `id` = " . $id;
            $row = $db->fetch($sql);
            return $row;
        }
    }

    /**
     * @return string
     */
    public function getNowVersion()
    {
        $db = $this->getDb();
        $sql = "SELECT `version` FROM `version` ORDER BY `version` DESC LIMIT 1";
        $row = $db->fetch($sql);
        if ($row) {
            $nowVersion = $row['version'];
            return $nowVersion;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getNextVersion()
    {
        $db = $this->getDb();

        //上周最大版本
        $time = strtotime('last Friday');
        $sql = "SELECT `version` FROM `version` WHERE `time`<={$time} ORDER BY `version` DESC LIMIT 1";
        $row = $db->fetch($sql);
        if ($row) {
            $nowVersion = $row['version'];
            $nextVersion = (new Func())->nextNum($nowVersion);
            return $nextVersion;
        } else {
            return '';
        }
    }
}