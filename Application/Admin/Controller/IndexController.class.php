<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        $this->assign('menus', D('Admin/Permission')->getAll(['parent_id' => 0]));
        $this->display();
    }
}