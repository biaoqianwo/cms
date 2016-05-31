<?php
namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller
{
    //登录
    public function login()
    {
        if (manage_id()) {
            $this->redirect('Index/index');
        }
        if (IS_POST) {
            $name = I('name');
            $pwd = I('pwd');
            /*$verify_code = I('verify_code');
            if (!$this->checkVerify($verify_code)) {
                $this->error('验证码不正确');
            }*/
            $rs = D('Admin/Manage')->login($name, $pwd);
            if ($rs['status']) {
                $this->redirect('Index/index');
            } else {
                $this->error($rs['info']);
            }
        } else {
            $this->assign('on_login_code', 0);
            $this->display();
        }
    }

    //退出
    public function logout()
    {
        D('Admin/Manage')->logout();
        $this->redirect('Public/login');
    }

    //图像
    public function verify()
    {
        $config = array('fontSize' => 15, 'length' => 3, 'useNoise' => false);
        $verify = new \Think\Verify($config);
        $verify->entry();
    }

    //图像验证
    protected function checkVerify($code)
    {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }


    public function validNameQQMobile()
    {
        $data = I('name');
        if (!$data) {
            $this->ajaxReturn(array('getdata' => "true"));
        }
        $model = D('Admin/Manage')->validNameQQMobile($data);
        $flag = $model ? "true" : "false";
        $rs = array('getdata' => $flag);
        $this->ajaxReturn($rs);
    }

}