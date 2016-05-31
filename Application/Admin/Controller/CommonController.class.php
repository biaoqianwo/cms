<?php
namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller
{

    //初始化
    public function _initialize()
    {
        $c = CONTROLLER_NAME; //控制器名Common/Product/ProductCate
        define('MID', manage_id());
        if (!MID) {
            $this->error('请先登陆', U('Public/login'));
        }
        $admin = D('Admin/Manage')->getOne(array('id' => MID));
        $this->assign('manage_auth', $admin);
        $permissions = explode(',', $admin['cate']['permissions']);
        $action = $c . "/" . ACTION_NAME;
        if (!in_array($action, $permissions) && MID != 1) {
            $this->error('没有权限', U('Index/index'));
        }

        //关联表
        if ($c != 'Index') {
            $relations = D('Admin/' . $c)->relationTable();
            //print_r($relations);
            foreach ($relations as $k => $v) {
                if ($c == biaoming2MVCname($k)) { //自己关联自己
                    $v = array_merge([['id' => 0, 'mingcheng' => '根']], D('Admin/Tree')->toFormatTree($v, 'mingcheng'));
                }
                $this->assign($k, $v);
            }
        }

    }


    public function index()
    {
        $limit = 20;
        $params = array('page' => I('p', 1), 'limit' => $limit);

        //查询条件
        $select = I('post.');
        foreach ($select as $k => $v) {
            $params[$k] = $v;
        }
        $this->assign('select', $select);
        $this->assign('datas', D('Admin/' . CONTROLLER_NAME)->getAll($params));

        //分页
        $page = new \Think\Page(D('Admin/' . CONTROLLER_NAME)->getCount($params), $limit);
        $this->assign('_page', $page->show());

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

    public function  view()
    {
        $id = I('id', 0);
        $data = D('Admin/' . CONTROLLER_NAME)->getOne(['id' => $id]);
        $this->assign('data', $data);
        $this->display();
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

    //上传图片
    public function upload()
    {
        $data = I('post.');
        $dir = '/' . $data['dir'] . '/';
        $upload = new \Think\Upload();
        $upload->rootPath = './Public/Uploads';
        $upload->savePath = $dir;
        $upload->saveName = date('YmdHis') . mt_rand(1000, 9999) . MID;
        $upload->autoSub = false;
        $info = $upload->uploadOne($_FILES['file']);
        echo !empty($info['savename']) ? $info['savename'] : '';
    }

    //删除图片
    public function uploaddel()
    {
        $src = I('post.src');
        @unlink($src);
        @unlink($src . '_430_430.jpg');
        @unlink($src . '_60_60.jpg');
        @unlink($src . '_300_300.jpg');
        @unlink($src . '_100_100.jpg');
        echo $this->ajaxReturn(1);
    }

    //上传附件
    public function uploadAttachment()
    {
        $dir = './Public/Uploads/Attachment/';
        $savename = date('YmdHis') . mt_rand(1, 9) . MID;
        $name = $_FILES['file']['name'];
        $upload = new \Think\Upload();
        $upload->rootPath = $dir;
        $upload->saveName = $savename;
        $upload->autoSub = false;
        $info = $upload->uploadOne($_FILES['file']);
        if (!$info['savename']) {
            echo '请上传zip,doc,docx,xls,xlsx,rar格式,小于10M的文件';
            return;
        }
        if ($info['ext'] == 'zip') {
            $zip = new \ZipArchive();
            if ($zip->open($dir . $info['savename']) !== TRUE) {
                echo '解压失败';
                return;
            }
            $zip->extractTo($dir . $savename . '/');
            $zip->close();
            @unlink($dir . $info['savename']);
        }
        $data = array('MID' => MID, 'name' => $name, 'savename' => $info['ext'] == 'zip' ? $savename : $savename . '.' . $info['ext'], 'ext' => $info['ext']);
        echo D('Admin/Attachment')->update($data);
    }

}