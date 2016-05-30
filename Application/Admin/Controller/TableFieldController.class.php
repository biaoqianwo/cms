<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 模型字段管理
 * Class TableFieldController
 * @package Admin\Controller
 */
class TableFieldController extends Controller
{
    //添加
    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $rs = D('Admin/TableField')->insert($data);
            if ($rs['status']) {
                $this->redirect('TableField/add', ['table_biaoming' => $data['table_biaoming']]);
            } else {
                $this->error($rs['info']);
            }
        } else {
            $table_biaoming = I('table_biaoming', '');
            if (!$table_biaoming) {
                $this->error('table_biaoming不能为空');
            }
            $data = D('Admin/Table')->getOne(['biaoming' => $table_biaoming]);
            $this->assign('data', $data);
            $this->assign('tables', D('Admin/Table')->getAll());
            $this->display();
        }
    }

    //编辑
    public function edit()
    {
        if (IS_POST) {
            $data = I('post.');
            $rs = D('Admin/TableField')->update($data);
            if ($rs['status']) {
                $this->redirect('TableField/edit', ['id' => $data['id']]);
            } else {
                $this->error($rs['info']);
            }
        } else {
            $id = I('id', '');
            if (!$id) {
                $this->error('id不能为空');
            }
            $field = D('Admin/TableField')->find($id);
            $data = D('Admin/Table')->getOne(['biaoming' => $field['table_biaoming']]);
            $data['field'] = $field;
            $this->assign('data', $data);

            $this->assign('tables', D('Admin/Table')->getAll());

            $this->display();
        }
    }

    //删除
    public function delete()
    {
        $id = I("id", 0);
        $this->ajaxReturn(D('Admin/' . CONTROLLER_NAME)->deleteOne($id));
    }

}