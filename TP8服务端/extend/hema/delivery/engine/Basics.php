<?php
namespace hema\delivery\engine;

/**
 * 引擎抽象类
 */
abstract class Basics
{
    protected $config;// 配置参数
    protected $isp;// 是否为服务商模式
    protected $error;// 错误信息

    /**
     * 构造函数
     */
    public function __construct($config,$isp=false)
    {
        $this->config = $config;
        $this->isp = $isp;
    }

    /**
     * 预发布订单
     */
    abstract protected function preOrder($data);

    /**
     * 添加订单
     */
    abstract protected function addOrder($data);

    /**
     * 取消订单
     */
    abstract protected function cancelOrder($order_no);
    
    /**
     * 返回错误信息
     */
    public function getError(): string
    {
        return $this->error;
    }

}
