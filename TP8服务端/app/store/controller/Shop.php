<?php
namespace app\store\controller;

use app\store\model\Setting as SettingModel;
use hema\map\Driver as MapDriver;
use hema\delivery\Driver as Delivery;
use think\facade\View;

/**
 * 门店设置
 */
class Shop extends Controller
{	
   /**
     * 门店设置
     */
    public function setting()
    {
        return $this->updateEvent('shop');
    }
    
    /**
     * 门店地图
     */
    public function getpoint(string $location='',string $shop_name='')
    {
        if(empty($location)){
            $map = new MapDriver;
            $location = $map->getIp();//坐标信息
        }else{
            $lat_lng = explode(',',$location);
            $location = [
                'location' => [
                    'lat' => $lat_lng[0],
                    'lng' => $lat_lng[1] 
                ],
                'shop_name' => $shop_name
            ];
        }
        $key = '';
    	$config = get_addons_config('qqmap');
    	if(isset($config['key'])){
    	    $key = $config['key'];
    	}
    	View::layout(false);
    	View::assign('wxmap_key', $key);
    	View::assign('location', $location);
        return View::fetch();
    }
    
	/**
     * 订单设置
     */
    public function order()
    {
        return $this->updateEvent('order');
    }
    
    /**
     * 商品设置
     */
    public function goods()
    {
        return $this->updateEvent('goods');
    }
    
    /**
     * WiFi设置
     */
    public function wifi()
    {
        return $this->updateEvent('wifi');
    }

    /**
     * 交易设置
     */
    public function trade()
    {
        return $this->updateEvent('trade');
    }
	
	/**
     * 配送设置
     */
    public function delivery()
    {
        $dv = new Delivery();
        $company = $dv->company();//获取配送公司列表
        View::assign('company',$company);//模板页面绑定数据
        return $this->updateEvent('delivery');
    }

    /**
     * 预约设置
     */
    public function pact()
    {
        return $this->updateEvent('pact');
    }
    /**
     * 更新设置事件
     */
    private function updateEvent(string $key)
    {
        if (!$this->request->isAjax()) {
			$model = SettingModel::getItem($key);
            return View::fetch($key, compact('model'));
        }
        $model = new SettingModel;
        if($model->edit($key, $this->postData('data'))) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

}
