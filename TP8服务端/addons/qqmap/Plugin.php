<?php
namespace addons\qqmap;

use think\Addons;

/**
 * 腾讯地图插件
 */
class Plugin extends Addons
{
    public $info = [
        'name' => 'qqmap',
        'title' => '腾讯地图',
        'description' => '坐标解析、距离计算、逆地址解析等',
        'status' => 1,
        'author' => '项目团队',
        'version' => '1.0.1',
    ];

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        $this->error = '底层功能插件，不允许删除';
        return false;
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
