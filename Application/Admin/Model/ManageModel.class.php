<?php

namespace Admin\Model;

use Think\Model;


class ManageModel extends CommonModel
{
    protected $_auto = array(
        array('created_at', NOW_TIME, self::MODEL_INSERT),
        array('update_at', NOW_TIME, self::MODEL_BOTH),
    );

    public function login($name, $pwd)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (!$name) {
            $rs['info'] = '用户名/邮箱/手机号码必须';
            return $rs;
        } elseif (!$pwd) {
            $rs['info'] = '密码必须';
            return $rs;
        }
        $model = $this->where(array('name|email|mobile' => $name, 'deleted_at' => 0))->find();
        if ($model) {
            if (think_md5($pwd) == $model['pwd']) {
                $this->autoLogin($model);
                return $model['id'];
            } else {
                $rs['info'] = '密码不正确';
                return $rs;
            }
        } else {
            $rs['info'] = '该用户名/邮箱/手机号码不存在,或已被删除';
            return $rs;
        }
    }

    private function autoLogin($model)
    {
        $last_login_at = $model['update_at'];
        $data = array(
            'id' => $model['id'],
            'login_count' => array('exp', '`login_count`+1'),
            'update_at' => NOW_TIME,
        );
        $this->save($data);
        $model['last_login_at'] = $last_login_at;
        $model['login_count'] = $model['login_count'] + 1;
        $model['update_at'] = NOW_TIME;
        unset($model['pwd']);
        session('manage_auth', $model);
        session('manage_auth_sign', data_auth_sign($model));
    }

    public function logout()
    {
        session('manage_auth', null);
        session('manage_auth_sign', null);
    }

    public function getOne($params = array())
    {
        $data = [];
        if ($params['id']) {
            $data = $this->find($params['id']);
        }
        //$data['role'] = D('Admin/Role')->getOne(array('code' => $data['role_code']));
        return $data;
    }

    public function insert($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (empty($params)) {
            $rs['info'] = '参数错误';
            return $rs;
        }
        if ($params['name']) {
            $model = $this->where(array('name' => $params['name']))->find();
            if ($model) {
                $rs['info'] = '用户名已经存在,请换一个';
                return $rs;
            }
        }
        if ($params['email']) {
            $model = $this->where(array('email' => $params['email']))->find();
            if ($model) {
                $rs['info'] = '邮箱已经存在,请换一个';
                return $rs;
            }
        }
        if ($params['mobile']) {
            $model = $this->where(array('mobile' => $params['mobile']))->find();
            if ($model) {
                $rs['info'] = '手机号码已经存在,请换一个';
                return $rs;
            }
        }
        $params['pwd'] = $params['pwd'] ? $params['pwd'] : '1234';
        $params['pwd'] = think_md5($params['pwd']);
        $this->create($params);
        $id = $this->add();
        if (!$id) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $id;
            $rs['info'] = '新增' . $params['name'];
            return $rs;
        }
    }

    //添加或编辑
    public function update($params)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '');
        if (empty($params)) {
            $rs['info'] = '参数错误';
            return $rs;
        } elseif (empty($params['id'])) {
            $rs['info'] = 'id必须';
            return $rs;
        }
        if ($params['name']) {
            $model = $this->where(array('name' => $params['name']))->find();
            if ($model && $model['id'] != $params['id']) {
                $rs['info'] = '用户名已经存在,请换一个';
                return $rs;
            }
        }
        if ($params['email']) {
            $model = $this->where(array('email' => $params['email']))->find();
            if ($model && $model['id'] != $params['id']) {
                $rs['info'] = '邮箱已经存在,请换一个';
                return $rs;
            }
        }
        if ($params['mobile']) {
            $model = $this->where(array('mobile' => $params['mobile']))->find();
            if ($model && $model['id'] != $params['id']) {
                $rs['info'] = '手机号码已经存在,请换一个';
                return $rs;
            }
        }

        $params['pwd'] = $params['pwd'] ? $params['pwd'] : '1234';
        $params['pwd'] = think_md5($params['pwd']);
        $this->create($params);
        $status = $this->save();
        if (!$status) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $params['id'];
            $rs['info'] = '修改' . $params['name'];
            return $rs;
        }
    }


    public function pwd($params)
    {
        $rs = array('status' => 0, 'info' => '');
        if (!$params['pwd']) {
            $rs['info'] = '新密码必须';
            return $rs;
        }
        if ($params['pwd'] != $params['repwd']) {
            $rs['info'] = '两次密码不相同,请重新输入';
            return $rs;
        }
        $status = $this->where(array('id' => AID))->save(array('pwd' => think_md5($params['pwd']), 'update_at' => NOW_TIME));
        if ($status) {
            $this->logout();
            $rs['status'] = 1;
        } else {
            $rs['info'] = $this->getError();
        }
        return $rs;
    }


    public function info($params)
    {
        $status = $this->where(array('id' => AID))->save(['email' => $params['email'], 'mobile' => $params['mobile'], 'update_at' => NOW_TIME]);
        return array('status' => $status ? 1 : 0, 'info' => $status ? '成功' : '失败');
    }


    public function resetpwd($id)
    {
        $status = $this->where(array('id' => $id))->save(array('pwd' => think_md5('1234'), 'update_at' => NOW_TIME));
        return array('status' => $status ? 1 : 0, 'info' => $status ? '成功' : '失败');
    }


}