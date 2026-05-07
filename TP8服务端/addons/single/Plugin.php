<?php
namespace addons\single;

use think\Addons;

/**
 * 河马点单 - 单商户版
 */
class Plugin extends Addons
{
    // 该插件的基础信息
    public $info = [
        'name' => 'single',	    // 插件标识
        'title' => '河马点单-单商户版',	// 插件名称
        'description' => '扫码点餐，排号点餐，外卖配送，排队订桌',	// 插件简介
        'status' => 0,	// 状态
        'author' => 'hemaPHP',
        'version' => '1.0.0'
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
        return true;
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