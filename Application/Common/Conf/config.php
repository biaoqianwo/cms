<?php
return array(
    'MODULE_ALLOW_LIST' => array('Admin'),
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'cms',
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'cms_',
    'DEFAULT_MODULE' => 'Admin', //变化的
    //'TMPL_ACTION_ERROR' => APP_PATH . 'Admin/View/Public/dispatch_jump.html', // 默认错误跳转对应的模板文件
    //'TMPL_EXCEPTION_FILE' => APP_PATH . 'Admin/View/Public/think_exception.html', // 异常页面的模板文件
    'TMPL_ACTION_SUCCESS' => APP_PATH . 'Admin/View/Public/dispatch_jump.html', // 默认成功跳转对应的模板文件
    'URL_MODEL' => 2,
    'URL_HTML_SUFFIX' => '',
);