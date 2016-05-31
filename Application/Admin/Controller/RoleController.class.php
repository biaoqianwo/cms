<?php
namespace Admin\Controller;

use Think\Controller;

class RoleController extends CommonController
{
    /**
     * 添加和编辑角色调用
     * @param $params
     */
    private function save($params)
    {
        //权限隐藏的赋给所有角色
        $datas = get_cache_list('MenuAdmin', 0, 'permission_display');
        $tmps = array();
        foreach ($datas as $data) {
            $tmps[] = $data['url'];
        }

        $permissions = explode(',', $params['permissions']);
        $permissions = array_merge($tmps, $permissions);
        $permissions = array_unique(array_filter($permissions));
        $params['permissions'] = implode(',', $permissions);

        $rs = D('Admin/AdminCate')->update($params);
        $rs['url'] = U('AdminCate/index');
        $this->ajaxReturn($rs);
    }

    /**
     * 添加角色
     */
    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $this->save($data);
        } else {
            $permission = D('Admin/Permission')->where(['quanxianxianshi' => 1])->select();
            $this->assign('permission', json_encode($permission));
            $this->display();
        }
    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        if (IS_POST) {
            $data = I('post.');
            $this->save($data);
        } else {
            $id = I('id', 0);
            $data = D('Admin/Role')->find($id);
            $this->assign('data', $data);

            $quanxian = explode(',', $data['quanxian']);
            $permission = D('Admin/Permission')->where(['quanxianxianshi' => 1])->select();
            foreach ($permission as &$v) {
                $v['checked'] = in_array($v['url'], $quanxian) ? 1 : 0;
            }
            $this->assign('permission', json_encode($permission));
            $this->display();
        }
    }

    /**
     * 查看角色
     */
    public function view()
    {
        $id = I('id', 0);
        $data = D('Admin/AdminCate')->getOne(array('id' => $id));
        $this->assign('data', $data);

        $permissions = explode(',', $data['permissions']);
        $datas = get_cache_list('MenuAdmin', 1, 'permission_display');
        foreach ($datas as &$v) {
            $v['checked'] = in_array($v['url'], $permissions) ? 1 : 0;
        }
        $this->assign('datas', json_encode(array_values($datas)));
        $this->display();
    }
}