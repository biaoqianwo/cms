<?php
namespace Admin\Controller;

use Think\Controller;

class PermissionController extends CommonController
{

   /* public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $rs = D('Admin/' . CONTROLLER_NAME)->insert($data);
            if ($rs['status']) {
                $this->redirect(CONTROLLER_NAME . '/index');
            } else {
                $this->error($rs['info']);
            }
        } else {
            $permission = D('Admin/' . CONTROLLER_NAME)->getAll();
            $permission = array_merge([['id' => 0, 'mingcheng' => '根']], D('Admin/Tree')->toFormatTree($permission, 'mingcheng'));
            $this->assign('permission', $permission);
            $this->display();
        }
    }

    public function edit()
    {
        if (IS_POST) {
            $data = I('post.');
            $rs = D('Admin/' . CONTROLLER_NAME)->update($data);
            if ($rs['status']) {
                $this->redirect(CONTROLLER_NAME . '/index');
            } else {
                $this->error($rs['info']);
            }
        } else {
            $permission = D('Admin/' . CONTROLLER_NAME)->getAll();
            $permission = array_merge([['id' => 0, 'mingcheng' => '根']], D('Admin/Tree')->toFormatTree($permission, 'mingcheng'));
            $this->assign('permission', $permission);

            $id = I('id', 0);
            $data = D('Admin/' . CONTROLLER_NAME)->getOne(['id' => $id]);
            $this->assign('data', $data);
            $this->display();
        }
    }

    public function delete()
    {
        $id = I("id", 0);
        $this->ajaxReturn(D('Admin/' . CONTROLLER_NAME)->deleteOne(['id' => $id]));
    }*/

}