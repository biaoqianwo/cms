<?php

namespace Admin\Model;

use Think\Model;

class TableFieldModel extends Model
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
        if ($params['table_biaoming']) {
            $where['table_biaoming'] = $params['table_biaoming'];
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
        if ($params['table_biaoming']) {
            $where['table_biaoming'] = $params['table_biaoming'];
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
        if ($params['leixing'] == 'table') {
            if (!$params['table']) {
                $rs['info'] = '关联表类型时必须选择表';
                return $rs;
            }
            $params['ziduanming_pinyin'] = $params['table'] . '_code';
        } else {
            $params['ziduanming_pinyin'] = quanpin($params['ziduanming']);
        }
        $flag = D('Admin/TableField')->where(['table_biaoming' => $params['table_biaoming'], 'ziduanming_pinyin' => $params['ziduanming_pinyin']])->find();
        if ($flag) {
            $rs['info'] = '字段名pinyin已经存在';
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
            $rs['info'] = '新增TableField' . $params['ziduanming'];

            $this->newfield($params);
            $this->updateView($params);

            return $rs;
        }
    }

    //新增字段
    protected function newfield($params)
    {
        $table = C('DB_PREFIX') . $params['table_biaoming'];
        $field = $params['ziduanming_pinyin'];
        $comment = $params['ziduanming'];
        $bitian = !empty($params['bitian']) ? 'NOT NULL' : 'NULL';
        switch ($params['leixing']) {
            case 'char':
            case 'table':
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}` VARCHAR(50) {$bitian} COMMENT '{$comment}';";
                break;
            case 'int':
            case 'date':
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}` INT(11) {$bitian} DEFAULT '0' COMMENT '{$comment}';";
                break;
            case 'text':
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}` TEXT {$bitian} COMMENT '{$comment}';";
                break;
            case 'longtext':
                $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$field}` LONGTEXT {$bitian} COMMENT '{$comment}';";
                break;
            default:
                $sql = "";
        }
        $this->execute($sql);
    }

    //更新MVC
    protected function updateView($params)
    {
        $fields = $this->getAll(['table_biaoming' => $params['table_biaoming']]);
        $str = ' <form data-toggle="validator" method="post" class="form-horizontal">';
        foreach ($fields as $field) {
            switch ($field['leixing']) {
                case 'char':
                    $str .= '<div class="form-group"><label class="col-sm-2 control-label">' . $field['ziduanming'] . '*</label><div class="col-sm-10"><input type="text" class="form-control" name="' . quanpin($field['ziduanming']) . '" placeholder="' . $field['ziduanming'] . '" required></div></div>';
            }
        }
        $str .= <<< eod
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary btn-block">提交</button>
            </div>
        </div>
    </form>
eod;
        $name = biaoming2MVCname($params['table_biaoming']);
        $html = file_get_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/add.html');
        $old = <<< eod
<a href="{:U('TableField/add',array('table_biaoming'=>ZZZ))}">添加字段</a>
eod;
        $old = str_replace('ZZZ', $params['table_biaoming'], $old);
        $html = str_replace($old, $str, $html);
        file_put_contents(APP_PATH . C('DEFAULT_MODULE') . '/View/' . $name . '/add.html', $html);
        //index.html

        //add.html

        //edit.html


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
        $this->create($params);
        $status = $this->save();
        if (!$status) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $params['id'];
            $rs['info'] = '修改tablefield' . $params['id'];
            return $rs;
        }
    }

    //删除
    public function deleteOne($id)
    {
        $rs = array('status' => 0, 'id' => 0, 'info' => '删除失败');
        $data = array('deleted_at' => NOW_TIME, 'updated_at' => NOW_TIME);
        $status = $this->where(array('id' => $id))->save($data);
        if (!$status) {
            $rs['info'] = $this->getError();
            return $rs;
        } else {
            $rs['status'] = 1;
            $rs['id'] = $id;
            $rs['info'] = '删除成功';
            return $rs;
        }
    }

}