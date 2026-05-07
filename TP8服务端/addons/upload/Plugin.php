<?php
namespace addons\upload;

use think\Addons;

/**
 * 文件上传插件
 */
class Plugin extends Addons
{
    public $info = [
        'name' => 'upload',
        'title' => '图库管理',
        'description' => '图片文件上传与管理',
        'status' => 0,
        'author' => '项目团队',
        'version' => '1.1.0',
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
