<?php
namespace hema\map\engine;

use app\common\model\Setting;

/**
 * 引擎抽象类
 */
abstract class Basics
{
    protected $config;// 配置参数
    protected $error;// 错误信息

    /**
     * 构造函数
     */
    public function __construct($config)
    {
        $this->config = $config;
    }
    
     /**
     * 计算距离
     * $from 起点，$to 终点
     * $mode 计算方式：driving（驾车）、walking（步行）bicycling：自行车
     */
    abstract protected function getDistance(string $from, string $to, string $mode='bicycling');
    
    /**
     * 逆地址解析(坐标位置描述)
     */
    abstract protected function getLocation(string $location);
    
    /**
     * IP定位
     */
    abstract protected function getIp(string $ip='');
    
    /**
     * 返回错误信息
     */
    public function getError(): string
    {
        return $this->error;
    }

}
