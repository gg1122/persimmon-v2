<?php
// 应用入口文件
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
//关闭调试模式
#define('APP_DEBUG',False);
// 定义应用目录
define('APP_PATH', './App/');

//定义安全目录
define('DIR_SECURE_FILENAME', 'index.html');

//定义Runtime目录
define('RUNTIME_PATH', './Runtime/');

//定义模版目录
define('TMPL_PATH', './Themes/');

// 引入ThinkPHP入口文件
require './Core/ThinkPHP.php';