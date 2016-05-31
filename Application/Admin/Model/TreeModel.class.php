<?php
namespace Admin\Model;

/**
 * 生成多层树状下拉选框的工具模型
 */
class TreeModel
{
    private $formatTree; //用于树型数组完成递归格式的全局变量

    //把数组转换成Tree
    private function list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }


    private function _toFormatTree($list, $level = 0, $name = 'name')
    {
        foreach ($list as $val) {
            $tmp_str = str_repeat("&nbsp;", $level * 2);
            $tmp_str .= "└";
            $val['level'] = $level;
            $val[$name] = $level == 0 ? $val[$name] . "&nbsp;" : $tmp_str . $val[$name] . "&nbsp;";
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $name); //进行下一层递归
            }
        }
        return;
    }

    public function toFormatTree($list, $name = 'name', $pk = 'id', $pid = 'parent_id', $root = 0)
    {
        $list = $this->list_to_tree($list, $pk, $pid, '_child', $root);
        $this->formatTree = array();
        $this->_toFormatTree($list, 0, $name);
        return $this->formatTree;
    }

}
