<?php
namespace hema\delivery;

use addons\dada\library\Dada;
use addons\hmpt\library\Hmpt;
use addons\make\library\Make;
use addons\sf\library\Sf;
use addons\shansong\library\Shansong;
use addons\uu\library\Uu;

/**
 * 第三方配送模块驱动
 */
class Driver
{
    private $engine;
    private $name;
    private $addons = [
        'hmpt' => '本地跑腿',
        'uu' => 'UU跑腿',
        'dada' => '达达快送',
        'sf' => '顺丰同城',
        'make' => '码科配送',
        'shansong' => '闪送',
    ];

    const ENGINE_CLASS_LIST = [
        'hmpt' => Hmpt::class,
        'uu' => Uu::class,
        'dada' => Dada::class,
        'sf' => Sf::class,
        'make' => Make::class,
        'shansong' => Shansong::class,
    ];

    public function __construct($name = '', $isp = false)
    {
        $this->name = $name;
        if (isset($this->addons[$name])) {
            if (!$config = \get_addons_config($name)) {
                die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$name] . '》插件'], JSON_UNESCAPED_UNICODE));
            }
            $class = self::ENGINE_CLASS_LIST[$name];
            $this->engine = new $class($config, $isp);
        }
    }

    public function company()
    {
        $company = [];
        foreach ($this->addons as $key => $item) {
            if ($dv = \get_addons_info($key)) {
                if ($dv['status'] == 1) {
                    $dv['config'] = \get_addons_config($key);
                    $company[] = $dv;
                }
            }
        }
        return $company;
    }

    public function preOrder($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->preOrder($data);
    }

    public function addOrder($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->addOrder($data);
    }

    public function cancelOrder($order_no)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->cancelOrder($order_no);
    }

    public function getCiytCode($city)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->getCiytCode($city);
    }

    public function riderPosition($order_no)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->riderPosition($order_no);
    }

    public function shopLists($search = '', $page = true)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->shopLists($search, $page);
    }

    public function shopDetail($shop_id)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->shopDetail($shop_id);
    }

    public function shopAdd($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->shopAdd($data);
    }

    public function shopEdit($data)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->shopEdit($data);
    }

    public function shopDelete($shop_id)
    {
        if (!isset($this->addons[$this->name])) {
            die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'], JSON_UNESCAPED_UNICODE));
        }
        return $this->engine->shopDelete($shop_id);
    }

    public function getError()
    {
        return $this->engine->getError();
    }
}
