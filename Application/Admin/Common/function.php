<?php
use Org\Util\Pinyin;

//汉字转化为全拼
//名称-->mingcheng
function quanpin($str = '')
{
    if (is_string($str)) {
        return strtolower(Pinyin::quanpin($str));
    } else {
        return '';
    }
}

//表名转化为MVC名称
//manage-->Manage,role_permission-->RolePermission
function biaoming2MVCname($biaoming = '')
{
    $biaoming = explode('_', $biaoming);
    foreach ($biaoming as $val) {
        $b[] = ucfirst($val);
    }
    $name = implode('', $b);
    return $name;
}

//系统非常规MD5加密方法
function think_md5($str, $key = 'cms')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

//判断管理员是是否登录
//管理员已登录返回管理员ID
//管理员未登录返回0
function manage_id()
{
    $s = session('manage_auth');
    return session('manage_auth_sign') == data_auth_sign($s) ? $s['id'] : 0;
}

//判断当前用户是否具有$action权限
function has_permission($action)
{
    return 1;
}

//记录操作日志
function insert_log($params)
{
    D('Admin/Log')->insert($params);
}

