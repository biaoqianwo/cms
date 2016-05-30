<?php
set_time_limit(0);
include_once('CExcel.php');

$excel = new CExcel(array(
    'cache' => __DIR__ . DIRECTORY_SEPARATOR . 'cache',
    'template' => __DIR__ . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'default.data',
    'column' => array('编号', '用户名', '昵称', '级别'),
    'columnCenter' => true, //是否居中
    'row' => 6, //开始写数据行
    'param' => array(
        1 => array( //参数 行
            1 => '影院：传奇时代影城', //参数 列
            4 => '制表日期：2015/9/9 17:09:11'
        ),
        2 => array(
            1 => '售票员销售报表',
        ),
        3 => array(
            1 => '操作日期: 从 2015/9/9 到 2015/9/9',
        ),
        4 => array(
            1 => '放映日期: 从 2015/9/10 到 2015/9/10'
        )
    ),
    'merge' => array('2,1:3,1', '1,2:4,2', '1,3:4,3', '1,4:4,4', '1,5:4,5'), //合并 列,行：列,行
    'height' => 18, //行高
    'width' => 30 //行宽
));

$db = mysqli_connect('192.168.3.172', 'root', '123456', 'tms');
$query = mysqli_query($db, 'SELECT count(*) as RecordCount FROM tms_user');
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

$recordCount = $data[0]['RecordCount'];
$pageSize = 2000;
$pageCount = ceil($recordCount / $pageSize) + 1;

for ($page = 1, $identity = 0; $page < $pageCount; $page++) {
    $identity = ($page - 1) * $pageSize;

    $query = mysqli_query($db, "SELECT uid,username,nickname,level FROM tms_user LIMIT {$identity}, {$pageSize}");

    $excel->load(mysqli_fetch_all($query, MYSQLI_ASSOC), $page);
}

$excel->create();
$excel->download();