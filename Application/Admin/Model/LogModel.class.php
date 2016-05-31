<?php
namespace Admin\Model;

use Think\Model;

class LogModel extends Model
{
    protected $_auto = array(
        array('created_at', NOW_TIME, self::MODEL_INSERT),
        array('update_at', NOW_TIME, self::MODEL_BOTH),
    );

    public function insert($params)
    {
        $data = ['manage_id' => $params['manage_id'], 'table' => $params['table'], 'table_id' => $params['id'], 'biaoti' => $params['info']];
        $this->create();
        $this->add($data);
    }
}