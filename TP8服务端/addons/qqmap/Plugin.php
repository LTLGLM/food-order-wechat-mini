<?php
namespace addons\qqmap;

use think\Addons;

/**
 * 腾讯地图插件
 */
class Plugin extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'qqmap',	// 插件标识
        'title' => '腾讯地图',	// 插件名称
        'description' => '坐标解析，距离计算，逆地址解析等',	// 插件简介
        'status' => 1,	// 状态
        'author' => 'hemaPHP',
        'version' => '1.0.1'
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