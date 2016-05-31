<?php

namespace Admin\Model;

use Think\Model;

class TableModel extends Model
{
    protected $_auto = array(
        array('created_at', NOW_TIME, self::MODEL_INSERT),
        array('updated_at', NOW_TIME, self::MODEL_BOTH),
    );

    public function getOne($params = [])
    {
        if ($params['id']) {
            $data = $this->find($params['id']);
        } elseif ($params['biaoming']) {
            $data = $this->where(['biaoming' => $params['biaoming']])->find();
        }
        $data['fields'] = D('Admin/TableField')->getAll(['table_biaoming' => $params['biaoming']]);
        return $data;
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
        return $datas;
    }

    //获取记录数
    public function getCount($params = array())
    {
        $where['deleted_at'] = 0;
        if ($params['kw']) {
            $where['mingcheng'] = array('like', '%' . $params['kw'] . '%');
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
        $params['biaoming'] = strtolower($params['biaoming']);
        $flag = $this->where(['biaoming' => $params['biaoming']])->find();
        if ($flag) {
            $rs['info'] = '表名已经存在';
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
            $rs['info'] = '新增' . $this->getTableName();

            $params['id'] = $rs['id'];
            $this->createTable($params);
            $this->insertTableField($params);
            $this->newMVC($params);
            $this->insertPermission($params);

            return $rs;
        }
    }

    //新增表
    protected function createTable($params)
    {
        $table = C('DB_PREFIX') . $params['biaoming'];
        $sql = "CREATE TABLE `{$table}` (`id` INT NOT NULL AUTO_INCREMENT,`created_at` INT NOT NULL DEFAULT '0',`updated_at` INT NOT NULL DEFAULT '0',`deleted_at` INT NOT NULL DEFAULT '0',PRIMARY KEY (`id`)) COMMENT='" . $params['mingcheng'] . "' COLLATE='utf8_general_ci' ENGINE=InnoDB;";
        $this->execute($sql);
    }

    protected function insertTableField($params)
    {
        $table = C('DB_PREFIX') . 'table_field';
        $table_biaoming = $params['biaoming'];
        $time = time();

        //主键ID
        $sql = "INSERT INTO `{$table}` (`table_biaoming`, `ziduanming`, `ziduanming_pinyin`, `leixing`, `weiyi`, `bitian`,`chaxunxianshi`,`liebiaoxianshi`,`created_at`,`updated_at`) VALUES ('{$table_biaoming}', '主键ID', 'id', 'int', 1, 0,1,0,{$time},{$time});";
        $this->execute($sql);

        $defaultField = [
            ['ziduanming' => 'created_at', 'comment' => '插入时间'],
            ['ziduanming' => 'updated_at', 'comment' => '更新时间'],
            ['ziduanming' => 'deleted_at', 'comment' => '删除时间'],
        ];
        foreach ($defaultField as $v) {
            $ziduanming = $v['ziduanming'];
            $comment = $v['comment'];
            $sql = "INSERT INTO `{$table}` (`table_biaoming`, `ziduanming`, `ziduanming_pinyin`, `leixing`, `weiyi`, `bitian`,`chaxunxianshi`,`liebiaoxianshi`,`created_at`,`updated_at`) VALUES ('{$table_biaoming}', '{$comment}', '{$ziduanming}', 'int', 0, 0,0,0,{$time},{$time});";
            $this->execute($sql);
        }
    }

    //新增MVC文件
    protected function newMVC($params)
    {
        $name = biaoming2MVCname($params['biaoming']);
        //model
        $model = $name . 'Model.class.php';
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/Model/' . $model)) {
            $str = <<< eod
<?php
namespace Admin\Model;

use Think\Model;

class XXXModel extends CommonModel
{
}
eod;
            $str = str_replace('XXX', $name, $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/Model/' . $model, $str);
        }

        //view
        if (!is_dir(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name)) {
            mkdir(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name);
        }

        //index.html
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/index.html')) {
            $str = <<< eod
<extend name="Public:front"/>
<block name="mianbaoxie">
    <li><a href="{:U('XXX/index')}">YYY</a></li>
    <if condition="has_permission('XXX/add')">
        <li><a href="{:U('XXX/add')}">添加</a></li>
    </if>
</block>
<block name="main">
    <a href="{:U('TableField/add',array('table_biaoming'=>ZZZ))}">添加字段</a>
</block>
eod;
            $str = str_replace('XXX', $name, $str);
            $str = str_replace('YYY', $params['mingcheng'], $str);
            $str = str_replace('ZZZ', $params['biaoming'], $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/index.html', $str);
        }

        //view.html
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/view.html')) {
            $str = <<< eod
<extend name="Public:front"/>
<block name="mianbaoxie">
    <li><a href="{:U('XXX/index')}">YYY</a></li>
</block>
<block name="main">
    <a href="{:U('TableField/add',array('table_biaoming'=>ZZZ))}">添加字段</a>
</block>
eod;
            $str = str_replace('XXX', $name, $str);
            $str = str_replace('YYY', $params['mingcheng'], $str);
            $str = str_replace('ZZZ', $params['biaoming'], $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/view.html', $str);
        }

        //add.html
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/add.html')) {
            $str = <<< eod
<extend name="Public:front"/>
<block name="mianbaoxie">
    <li><a href="{:U('XXX/index')}">YYY</a></li>
</block>
<block name="main">
    <a href="{:U('TableField/add',array('table_biaoming'=>ZZZ))}">添加字段</a>
</block>
eod;
            $str = str_replace('XXX', $name, $str);
            $str = str_replace('YYY', $params['mingcheng'], $str);
            $str = str_replace('ZZZ', $params['biaoming'], $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/add.html', $str);
        }

        //edit.html
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/edit.html')) {
            $str = <<< eod
<extend name="Public:front"/>
<block name="mianbaoxie">
    <li><a href="{:U('XXX/index')}">YYY</a></li>
</block>
<block name="main">
    <a href="{:U('TableField/add',array('table_biaoming'=>ZZZ))}">添加字段</a>
</block>
eod;
            $str = str_replace('XXX', $name, $str);
            $str = str_replace('YYY', $params['mingcheng'], $str);
            $str = str_replace('ZZZ', $params['biaoming'], $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/edit.html', $str);
        }
        //controller
        $controller = $name . 'Controller.class.php';
        if (!is_file(APP_PATH . C('DEFAULT_MODULE') . '/Controller/' . $controller)) {
            $str = <<< eod
<?php
namespace Admin\Controller;

use Think\Controller;

class XXXController extends CommonController
{
}
eod;
            $str = str_replace('XXX', $name, $str);
            file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/Controller/' . $controller, $str);
        }
    }

    //插入权限
    protected function insertPermission($params)
    {
        $name = biaoming2MVCname($params['biaoming']);

        //增删改查CRUD
        $data = ['fuji' => 0, 'mingcheng' => $params['mingcheng'] . '管理', 'url' => $name . '/index', 'caidanxianshi' => 1, 'quanxianxianshi' => 1];
        $rs = D('Admin/Permission')->insert($data);
        if ($rs['status']) {
            $datas[] = ['fuji' => $params['mingcheng'] . '管理', 'mingcheng' => $params['mingcheng'] . '查看', 'url' => $name . '/view', 'caidanxianshi' => 0, 'quanxianxianshi' => 1, 'created_at' => time(), 'updated_at' => time()];
            $datas[] = ['fuji' => $params['mingcheng'] . '管理', 'mingcheng' => $params['mingcheng'] . '添加', 'url' => $name . '/add', 'caidanxianshi' => 0, 'quanxianxianshi' => 1, 'created_at' => time(), 'updated_at' => time()];
            $datas[] = ['fuji' => $params['mingcheng'] . '管理', 'mingcheng' => $params['mingcheng'] . '编辑', 'url' => $name . '/edit', 'caidanxianshi' => 0, 'quanxianxianshi' => 1, 'created_at' => time(), 'updated_at' => time()];
            $datas[] = ['fuji' => $params['mingcheng'] . '管理', 'mingcheng' => $params['mingcheng'] . '删除', 'url' => $name . '/delete', 'caidanxianshi' => 0, 'quanxianxianshi' => 1, 'created_at' => time(), 'updated_at' => time()];
            D('Admin/Permission')->addAll($datas);
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
        $params['biaoming'] = strtolower($params['biaoming']);
        $flag = $this->where(['biaoming' => $params['biaoming']])->find();
        if ($flag) {
            $rs['info'] = '表名已经存在';
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
            $rs['info'] = '修改' . $this->getTableName();
            //ALTER TABLE `cms_test` COMMENT='测试2表';
            //RENAME TABLE `cms_test` TO `cms_test2`;
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
            $rs['info'] = '删除' . $this->getTableName();
            return $rs;
        }
    }
}