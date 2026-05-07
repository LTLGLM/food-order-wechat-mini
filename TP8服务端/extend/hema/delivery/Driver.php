<?php
namespace hema\delivery;

use addons\hmpt\library\Hmpt;
use addons\uu\library\Uu;
use addons\dada\library\Dada;
use addons\sf\library\Sf;
use addons\make\library\Make;
use addons\shansong\library\Shansong;

/**
 * 第三方配送模块驱动
 */
class Driver
{
    private $engine;    // 当前引擎类
    private $name;
    private $addons = [ //扩展插件
        'hmpt' => '河马跑腿',
        'uu' => 'UU跑腿',
        'dada' => '达达快送',
        'sf' => '顺丰同城',
        'make' => '码科配送',
        'shansong' => '闪送',
    ];
    /**
     * 云设备引擎类列表
     */
    const ENGINE_CLASS_LIST = [
        'hmpt' => Hmpt::class,
        'uu' => Uu::class,
        'dada' => Dada::class,
        'sf' => Sf::class,
        'make' => Make::class,
        'shansong' => Shansong::class,
    ];

    /**
     * 构造方法
     * @param null|string $name 扩展插件名称
     */
    public function __construct($name = '',$isp=false)
    {
        $this->name = $name;
        if(isset($this->addons[$name])){
            // 实例化当前引擎
            if(!$config = \get_addons_config($name)){
                die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$name] .'》插件'],JSON_UNESCAPED_UNICODE));
            }
            $class = self::ENGINE_CLASS_LIST[$name];
            $this->engine = new $class($config,$isp);
        }
    }
    
    /**
     * 获取已启用的插件列表
     */
    public function company()
    {
        $company = [];
        foreach ($this->addons as $key=>$item) {
            if($dv = \get_addons_info($key)){
                if($dv['status'] == 1){
                    $dv['config'] = \get_addons_config($key);
                    $company[] = $dv;
                }
            }
        }
        return $company;
    }
    /**
     * 预发布订单
     */
    public function preOrder($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->preOrder($data);
    }

    /**
     * 添加订单
     */
    public function addOrder($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->addOrder($data);
    }
    /**
     * 取消订单
     */
    public function cancelOrder($order_no)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->cancelOrder($order_no);
    }
    /**
     * 获取城市编号
     */
    public function getCiytCode($city)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->getCiytCode($city);
    }
    /**
     * 获取骑手实时位置
     */
    public function riderPosition($order_no)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->riderPosition($order_no);
    }
    /**
     * 获取门店列表
     */
    public function shopLists($search='',$page=true)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->shopLists($search,$page);
    }

    /**
     * 获取门店详情
     */
    public function shopDetail($shop_id)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->shopDetail($shop_id);
    }

    /**
     * 添加门店
     */
    public function shopAdd($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->shopAdd($data);
    }
    
    /**
     * 编辑门店
     */
    public function shopEdit($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->shopEdit($data);
    }
    
    /**
     * 删除门店
     */
    public function shopDelete($shop_id)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->shopDelete($shop_id);
    }
    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

}
