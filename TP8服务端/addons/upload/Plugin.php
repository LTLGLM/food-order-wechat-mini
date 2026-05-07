<?php
namespace addons\upload;

use think\Addons;

/**
 * 文件上传插件
 */
class Plugin extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'upload',	// 插件标识
        'title' => '文件上传',	// 插件名称
        'description' => '图片文件上传与管理',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'hemaPHP',
        'version' => '1.1.0'
    ];
	
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
		$this->error = '底层功能插件，不允许删除';
        return false;
    }

    /**
     * 插件升级方法
     * @return bool
     */
    public function upgrade()
    {
        return true;
    }
	
    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        return true;
    }

}