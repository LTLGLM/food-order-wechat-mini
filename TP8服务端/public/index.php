<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// 检测PHP环境
//if (version_compare(PHP_VERSION, '7.4.0', '<')) die('require PHP > 7.1.0 !');
// 检测php版本号
if (phpversion() < '7.4') {
    exit('很抱歉，由于您的PHP版本过低，部分功能无法运行，为了系统功能全面可用，请升级到PHP7.4或更高版本，谢谢！');
}
define('INSTALL_URL', str_replace('\\', '/', dirname(__FILE__) . '/install/'));
// 判断是否安装
if (!is_file(INSTALL_URL . 'install.lock')) {
    header("location:/install");
    exit;
}

// 加载核心文件
require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);

