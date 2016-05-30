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

    protected function _initialize()
    {
        //考虑缓存
        $this->tableFields = D('Admin/TableField')->where(['table_biaoming' => strtolower($this->getModelName())])->select();
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
                    //图片地址
                    //$data[$field['ziduanming_pinyin'] . '_url'] = '/Public/Uploads/' . $this->getModelName() . '/' . $data[$field['ziduanming_pinyin']];
                    break;
                case 'text':
                    $data[$field['ziduanming_pinyin']] = htmlspecialchars_decode($data[$field['ziduanming_pinyin']]);
                    break;
                case 'longtext':
                    $data[$field['ziduanming_pinyin']] = htmlspecialchars_decode($data[$field['ziduanming_pinyin']]);
                    break;
                default:
                    ;
            }
        }
        return $data;
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

    //查询一条记录
    public function getOne($params)
    {
        if ($params['id']) {
            $data = $this->find($params['id']);
        } else if ($params['mingcheng']) {
            $data = $this->where(['mingcheng' => $params['mingcheng']])->find();
        } else if ($params['biaoti']) {
            $data = $this->where(['biaoti' => $params['biaoti']])->find();
        } else if ($params['code']) {
            $data = $this->where(['code' => $params['code']])->find();
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
            //$data['href'] = U($data['url']);
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

    //添加
    public function insert($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (empty($params)) {
            $rs['info'] = '参数不能为空';
            return $rs;
        }
        $params = $this->insertUpdateFormat($params);
        $this->create($params);
        $id = $this->add();
        if (!$id) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $id;
            $rs['info'] = '新增' . $params['url'];
            return $rs;
        }
    }

    //编辑
    public function update($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (empty($params)) {
            $rs['info'] = '参数不能为空';
            return $rs;
        } elseif (empty($params['id'])) {
            $rs['info'] = 'id必须存在';
            return $rs;
        }
        $params = $this->insertUpdateFormat($params);
        $this->create($params);
        $status = $this->save();
        if (!$status) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $params['id'];
            $rs['info'] = '修改' . $params['url'];
            return $rs;
        }
    }

    //删除
    public function deleteOne($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '删除失败');
        $data = array('deleted_at' => NOW_TIME, 'updated_at' => NOW_TIME);
        $status = $this->where(array('id' => $params['id']))->save($data);
        if (!$status) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $params['id'];
            $rs['info'] = '删除成功';
            return $rs;
        }
    }

    //关联表
    public function relationTable()
    {
        $fields = $this->tableFields;
        foreach ($fields as $field) {
            if ($field['leixing'] == 'table') {
                $table = $field['table'];
                $model = biaoming2MVCname($table);
                $rs[$table.'s'] = D('Admin/' . $model)->getAll();
            }
        }
        return $rs;
    }
}