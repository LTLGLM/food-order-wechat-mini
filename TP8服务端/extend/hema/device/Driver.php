<?php
namespace hema\device;

use addons\daqu\library\Daqu;
use addons\feieyun\library\Feieyun;
use addons\hmcalling\library\Hmcalling;
use addons\yilianyun\library\Yilianyun;

/**
 * 云设备模块驱动
 */
class Driver
{
    private $engine;
    private $name;
    private $addons = [
        'feieyun' => '飞鹅云打印',
        'yilianyun' => '易联云打印',
        'daqu' => '大趋云打印',
        'hmcalling' => '云叫号',
    ];

    const ENGINE_CLASS_LIST = [
        'feieyun' => Feieyun::class,
        'yilianyun' => Yilianyun::class,
        'daqu' => Daqu::class,
        'hmcalling' => Hmcalling::class,
    ];

    public function __construct($name = '')
    {
        $this->name = $name;
        if (isset($this->addons[$name])) {
            if (!$config = \get_addons_config($name)) {
                die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$name] . '》插件'], JSON_UNESCAPED_UNICODE));
            }
            $class = self::ENGINE_CLASS_LIST[$name];
            $this->engine = new $class($config);
        }
    }

    public function company()
    {
        $company = [];
        foreach ($this->addons as $key => $item) {
            if ($info = \get_addons_info($key)) {
                if ($info['status'] == 1) {
                    $info['config'] = \get_addons_config($key);
                    $company[] = $info;
                }
            }
        }
        return $company;
    }

    public function add($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->add($data);
    }

    public function status($dev_id)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->status($dev_id);
    }

    public function delete($dev_id)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->delete($dev_id);
    }

    public function edit($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->edit($data);
    }

    public function push($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->push($data);
    }

    public function print($dev, $content)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->print($dev, $content);
    }

    public function getError()
    {
        return $this->engine->getError();
    }
}
