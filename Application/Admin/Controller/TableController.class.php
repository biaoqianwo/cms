<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 模型管理
 * Class TableController
 * @package Admin\Controller
 */
class TableController extends Controller
{
    public function index()
    {
        $datas = D('Admin/' . CONTROLLER_NAME)->getAll();
        $this->assign('datas', $datas);
        $this->display();
    }

    public function add()
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
    }


}