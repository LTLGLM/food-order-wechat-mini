<?php
namespace addons\single;

use think\Addons;

/**
 * 餐饮点单 - 单商户版
 */
class Plugin extends Addons
{
    public $info = [
        'name' => 'single',
        'title' => '餐饮点单-单商户版',
        'description' => '扫码点餐，排号点餐，外卖配送，排队订桌',
        'status' => 0,
        'author' => '项目团队',
        'version' => '1.0.0',
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function upgrade()
    {
        return true;
    }

    public function enable()
    {
        return true;
    }

    public function disable()
    {
        return true;
    }
}
