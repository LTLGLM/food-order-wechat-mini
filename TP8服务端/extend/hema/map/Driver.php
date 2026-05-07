<?php
namespace hema\map;

use app\common\model\Setting;
use addons\qqmap\library\Qqmap;
use addons\baidumap\library\Baidumap;
use addons\amap\library\Amap;

/**
 * 地图驱动
 */
class Driver
{
    private $engine;    // 当前引擎类
    private $addons = [ //扩展插件
        'qqmap' => '腾讯地图',      //GCJ-02坐标系
        'baidumap' => '百度地图',   //BD-09坐标系
        'amap' => '高德地图',       //GCJ-02坐标系
    ];
    private $gateway;
    /**
     * 引擎类列表
     */
    const ENGINE_CLASS_LIST = [
        'qqmap' => Qqmap::class,
        'baidumap' => Baidumap::class,
        'amap' => Amap::class
    ];
    
     /**
     * 构造方法
     */
    public function __construct()
    {
        $this->gateway = Setting::getItem('map')['gateway'];
        if(isset($this->addons[$this->gateway])){
            // 实例化当前引擎
            if(!$config = get_addons_config($this->gateway)){
                die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$this->gateway] .'》插件'],JSON_UNESCAPED_UNICODE));
            }
            $class = self::ENGINE_CLASS_LIST[$this->gateway];
            $this->engine = new $class($config);
        }
    }
    
    /**
     * 获取已启用的插件列表
     */
    public function company()
    {
        $company = [];
        foreach ($this->addons as $key=>$item) {
            if($info = get_addons_info($key)){
                if($info['status'] == 1){
                    $info['config'] = get_addons_config($key);
                    $company[] = $info;
                }
            }
        }
        return $company;
    }
    
    /**
     * 计算距离
     * $from 起点，$to 终点
     * $mode 计算方式：driving（驾车）、walking（步行）bicycling：自行车
     */
    public function getDistance(string $from, string $to, string $mode='bicycling')
    {
        if(!isset($this->addons[$this->gateway])){
           die(json_encode(['code' => 0, 'msg' => '未配置地图平台'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->getDistance($from, $to, $mode);
    }
    
    /**
     * 逆地址解析(坐标位置描述)
     */
    public function getLocation(string $location)
    {
        if(!isset($this->addons[$this->gateway])){
           die(json_encode(['code' => 0, 'msg' => '未配置地图平台'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->getLocation($location);
    }
    
    /**
     * IP定位
     */
    public function getIp(string $ip='')
    {
        if(!isset($this->addons[$this->gateway])){
           die(json_encode(['code' => 0, 'msg' => '未配置地图平台'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->getIp($ip);
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
