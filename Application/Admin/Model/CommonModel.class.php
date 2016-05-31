<?php

namespace Admin\Model;

use Think\Model;

class CommonModel extends Model
{
    protected $_auto = array(
        array('created_at', NOW_TIME, self::MODEL_INSERT),
        array('updated_at', NOW_TIME, self::MODEL_BOTH),
    );

    public $tableFields = [];

    //初始化
    protected function _initialize()
    {
        $table_biaoming = strtolower($this->getModelName());
        if (S('tablefield_' . $table_biaoming)) {
            $data = S('tablefield_' . $table_biaoming);
        } else {
            $data = D('Admin/TableField')->where(['table_biaoming' => $table_biaoming])->select();
        }
        $this->tableFields = $data;
    }

    //组装wheret条件
    protected function geneWhere($params = [])
    {
        $fields = $this->tableFields;
        $where['deleted_at'] = 0;
        foreach ($fields as $field) {
            switch ($field['leixing']) {
                case 'table':
                case 'int':
                    if ($params[$field['ziduanming_pinyin']]) {
                        $where[$field['ziduanming_pinyin']] = $params[$field['ziduanming_pinyin']];
                    }
                    break;
                case 'date':
                    if ($params[$field['ziduanming_pinyin'] . '_start'] || $params[$field['ziduanming_pinyin'] . '_end']) {
                        $start = $params[$field['ziduanming_pinyin'] . '_start'] ? $params[$field['ziduanming_pinyin'] . '_start'] : 0;
                        $end = $params[$field['ziduanming_pinyin'] . '_end'] ? $params[$field['ziduanming_pinyin'] . '_end'] : 2145888000; //2038-01-01
                        $where[$field['ziduanming_pinyin']] = [['GT' => $start, 'LT' => $end]];
                    }
                    break;
                case 'char':
                case 'text':
                    //case 'longtext':
                    //case 'img':
                    if ($params[$field['ziduanming_pinyin']]) {
                        $where[$field['ziduanming_pinyin']] = array('like', '%' . $params[$field['ziduanming_pinyin']] . '%');
                    }
                    break;
                default:
                    ;
            }
        }
        return $where;
    }

    //结果格式化
    protected function resultFormat($data)
    {
        $fields = $this->tableFields;
        foreach ($fields as $field) {
            switch ($field['leixing']) {
                case 'date':
                    $data[$field['ziduanming_pinyin']] = date('Y-m-d', $data[$field['ziduanming_pinyin']]);
                    break;
                case 'img':
                    //$data[$field['ziduanming_pinyin'] . '_url'] = '/Public/Uploads/' . $this->getModelName() . '/' . $data[$field['ziduanming_pinyin']];
                    break;
                case 'text':
                case 'longtext':
                    $data[$field['ziduanming_pinyin']] = htmlspecialchars_decode($data[$field['ziduanming_pinyin']]);
                    break;
                default:
                    ;
            }
        }
        return $data;
    }

    //插入更新格式化
    protected function insertUpdateFormat($data)
    {
        $fields = $this->tableFields;
        foreach ($fields as $field) {
            switch ($field['leixing']) {
                case 'date':
                    $data[$field['ziduanming_pinyin']] = strtotime($data[$field['ziduanming_pinyin']]);
                    break;
                default:
                    ;
            }
        }
        return $data;
    }

    //插入更新验证
    protected function insertUpdateCheck($data)
    {
        $fields = $this->tableFields;
        $rs = ['status' => 1, 'info' => ''];
        foreach ($fields as $field) {
            if ($field['weiyi']) {
                $flag = $this->where([(string)$field['ziduanming_pinyin'] => $data[$field['ziduanming_pinyin']]])->find();
                if (!$data['id']) { //insert
                    if ($flag) {
                        $rs['status'] = 0;
                        $rs['info'] = $field['ziduanming'] . '要求唯一,和ID=' . $flag['id'] . '重复';
                        return $rs;
                    }
                } else { //update
                    if ($flag && $flag['id'] != $data['id']) {
                        $rs['status'] = 0;
                        $rs['info'] = $field['ziduanming'] . '要求唯一,和ID=' . $flag['id'] . '重复';
                        return $rs;
                    }
                }
            }
        }
        return $rs;
    }

    //查询一条记录
    public function getOne($params)
    {
        if ($params['id']) {
            $data = $this->find($params['id']);
        } else if ($params['mingcheng']) {
            $data = $this->where(['mingcheng' => $params['mingcheng']])->find();
        } else if ($params['biaoti']) {
            $data = $this->where(['biaoti' => $params['biaoti']])->find();
        } else {
            return [];
        }
        $data = $this->resultFormat($data);
        return $data;
    }

    //查询多条记录
    public function getAll($params = [])
    {
        $limit = $params['limit'] > 0 ? $params['limit'] : 20;
        $page = $params['page'] > 0 ? ($params['page'] - 1) * $limit : -1;
        $where = $this->geneWhere($params);
        $order = $params['order'] ? $params['order'] : 'id desc';
        $datas = $this->where($where)->order($order);
        $datas = $page >= 0 ? $datas->limit($page, $limit)->select() : $datas->select();
        foreach ($datas as &$data) {
            $data = $this->resultFormat($data);
        }
        return $datas;
    }

    //获取记录数
    public function getCount($params = array())
    {
        $where = $this->geneWhere($params);
        return $this->where($where)->count();
    }

    //添加
    public function insert($params)
    {
        $table = $this->getTableName();
        $rs = ['status' => 0, 'id' => 0, 'info' => ''];
        if (empty($params)) {
            $rs['info'] = '参数不能为空';
            return $rs;
        }
        $flag = $this->insertUpdateCheck($params);
        if (!$flag['status']) {
            $rs['info'] = $flag['info'];
            return $rs;
        }
        $params = $this->insertUpdateFormat($params);
        $this->create($params);
        $id = $this->add();
        if (!$id) {
            $rs['info'] = "新增{$table}失败";
        } else {
            $rs = ['status' => 1, 'id' => $id, 'info' => "新增{$table}成功"];
        }
        insert_log(['manage_id' => manage_id(), 'table' => $table, 'table_id' => $id, 'biaoti' => $rs['info']]);
        return $rs;
    }

    //编辑
    public function update($params)
    {
        $table = $this->getTableName();
        $rs = ['status' => 0, 'id' => 0, 'info' => ''];
        if (empty($params)) {
            $rs['info'] = '参数不能为空';
            return $rs;
        } elseif (empty($params['id'])) {
            $rs['info'] = 'id必须存在';
            return $rs;
        }
        $flag = $this->insertUpdateCheck($params);
        if (!$flag['status']) {
            $rs['info'] = $flag['info'];
            return $rs;
        }
        $params = $this->insertUpdateFormat($params);
        $this->create($params);
        $status = $this->save();
        if (!$status) {
            $rs['info'] = "编辑{$table}失败";
        } else {
            $rs = ['status' => 1, 'id' => $params['id'], 'info' => "编辑{$table}成功"];
        }
        insert_log(['manage_id' => manage_id(), 'table' => $table, 'table_id' => $params['id'], 'biaoti' => $rs['info']]);
        return $rs;
    }

    //删除
    public function deleteOne($params)
    {
        $table = $this->getTableName();
        $rs = ['status' => 0, 'id' => 0, 'info' => ''];
        $status = $this->where(array('id' => $params['id']))->save(['deleted_at' => NOW_TIME, 'updated_at' => NOW_TIME]);
        if (!$status) {
            $rs['info'] = "删除{$table}失败";
        } else {
            $rs = ['status' => 1, 'id' => $params['id'], 'info' => "删除{$table}成功"];
        }
        insert_log(['manage_id' => manage_id(), 'table' => $table, 'table_id' => $params['id'], 'biaoti' => $rs['info']]);
        return $rs;
    }

    //关联表
    public function relationTable()
    {
        $fields = $this->tableFields;
        foreach ($fields as $field) {
            if ($field['leixing'] == 'table') {
                $table = $field['table'];
                $model = biaoming2MVCname($table);
                $rs[$table] = D('Admin/' . $model)->getAll();
            }
        }
        return $rs;
    }
}