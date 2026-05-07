<?php
namespace hema\device;

use addons\hmcalling\library\Hmcalling;
use addons\feieyun\library\Feieyun;
use addons\yilianyun\library\Yilianyun;
use addons\daqu\library\Daqu;

/**
 * 云设备模块驱动
 */
class Driver
{
    private $engine;    // 当前设备擎类
    private $name;
    private $addons = [ //扩展插件
        'feieyun' => '飞鹅云打印',
        'yilianyun' => '易联云打印',
        'daqu' => '大趋云打印',
        'hmcalling' => '河马云叫号'
    ];
    /**
     * 云设备引擎类列表
     */
    const ENGINE_CLASS_LIST = [
        'feieyun' => Feieyun::class,
        'yilianyun' => Yilianyun::class,
        'daqu' => Daqu::class,
        'hmcalling' => Hmcalling::class
    ];
    
     /**
     * 构造方法
     * @param null|string $name 扩展插件名称
     */
    public function __construct($name='')
    {
        $this->name = $name;
        if(isset($this->addons[$name])){
            // 实例化当前引擎
            if(!$config = \get_addons_config($name)){
                die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$name] .'》插件'],JSON_UNESCAPED_UNICODE));
            }
            $class = self::ENGINE_CLASS_LIST[$name];
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
            if($info = \get_addons_info($key)){
                if($info['status'] == 1){
                    $info['config'] = \get_addons_config($key);
                    $company[] = $info;
                }
            }
        }
        return $company;
    }
    
    /**
     * 添加设备
     * $data = 设备参数
     */
    public function add($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->add($data);
    }
    
    /**
     * 获取设备状态
     * $dev_id = 设备ID
     */
    public function status($dev_id)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->status($dev_id);
    }

    /**
     * 删除设备
     * $dev_id = 设备ID
     */
    public function delete($dev_id)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->delete($dev_id);
    }
    
    /**
     * 编辑设备
     */
    public function edit($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->edit($data);
    }
    
    /**
     * 播报语音
     */
    public function push($data)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->push($data);
    }
    
    /**
     * 执行打印
     */
    public function print($dev,$content)
    {
        if(!isset($this->addons[$this->name])){
           die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE)); 
        }
        return $this->engine->print($dev,$content);
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
