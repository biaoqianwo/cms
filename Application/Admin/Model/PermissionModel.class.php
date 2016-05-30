<?php

namespace Admin\Model;

use Think\Model;

class PermissionModel extends Model
{
    protected $_auto = array(
        array('created_at', NOW_TIME, self::MODEL_INSERT),
        array('updated_at', NOW_TIME, self::MODEL_BOTH),
    );

    public function getOne($params = [])
    {
        if ($params['id']) {
            return $this->find($params['id']);
        }
    }

    // 获取记录
    public function getAll($params = array())
    {
        $where['deleted_at'] = 0;
        if ($params['limit']) {
            $limit = intval($params['limit']);
        } else {
            $limit = 10;
        }
        if ($params['page']) {
            $page = (intval($params['page']) - 1) * $limit;
        } else {
            $page = -1;
        }
        if ($params['kw']) {
            $where['mingcheng'] = array('like', '%' . $params['kw'] . '%');
        }
        if(isset($params['fuji'])){
            $where['fuji'] = $params['fuji'];
        }
        $order = 'id desc';
        if ($params['order']) {
            $order = $params['order'];
        }
        $datas = $this->where($where)->order($order);
        if ($page >= 0) {
            $datas = $datas->limit($page, $limit)->select();
        } else {
            $datas = $datas->select();
        }
        foreach($datas as &$data){
            $data['href'] = U($data['url']);
        }
        return $datas;
    }

    //获取记录数
    public function getCount($params = array())
    {
        $where['deleted_at'] = 0;
        if ($params['kw']) {
            $where['mingcheng'] = array('like', '%' . $params['kw'] . '%');
        }
        if(isset($params['fuji'])){
            $where['fuji'] = $params['fuji'];
        }
        return $this->where($where)->count();
    }

    //添加
    public function insert($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (empty($params)) {
            $rs['info'] = '参数不能为空';
            return $rs;
        }
        $params['url'] = strtolower($params['url']);
        $flag = $this->where(['url' => $params['url']])->find();
        if ($flag) {
            $rs['info'] = 'url已经存在';
            return $rs;
        }
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
        $params['url'] = strtolower($params['url']);
        $flag = $this->where(['url' => $params['url']])->find();
        if ($flag) {
            $rs['info'] = 'url已经存在';
            return $rs;
        }
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
}