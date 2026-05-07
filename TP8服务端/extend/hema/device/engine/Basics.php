<?php
namespace hema\device\engine;

use app\common\model\Setting;

/**
 * 设备引擎抽象类
 */
abstract class Basics
{
    protected $config;// 云平台参数
    protected $error;// 错误信息

    /**
     * 构造函数
     */
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    /**
     * 添加设备
     */
    abstract protected function add($data);

    /**
     * 获取设备状态
     */
    abstract protected function status($dev_id);

    /**
     * 删除设备
     */
    abstract protected function delete($dev_id);
    
    /**
     * 执行打印
     */
    //abstract protected function print();

    /**
     * 播报语音
     */
    //abstract protected function push();
    
    /**
     * 获取token
     */
    //abstract protected function getToken();
    /**
     * 编辑设备
     */
    //abstract protected function edit();

    /**
     * 返回错误信息
     */
    public function getError(): string
    {
        return $this->error;
    }

}
