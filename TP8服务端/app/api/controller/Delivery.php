<?php
namespace app\api\controller;

use app\api\model\Order as OrderModel;

/**
 * 第三方配送异步通讯接口
 */
class Delivery
{
    /**
     * 河马跑腿异步通知处理
     */
    public function hmpt()
    {
        $data = file_get_contents('php://input');
		$data = json_decode($data,true);
		//判断是否为订单回调
		if(isset($data['callback_type']) and $data['callback_type']=='order'){
		    $model = (new OrderModel)->where('order_no',$data['shop_order_no'])->find();
		    $model->updateDeliveryStatus($data,'hmpt');
		}
        return true;
    }
    
	/**
     * 顺丰异步通知处理
     */
    public function sf()
    {
        $data = file_get_contents('php://input');
        $sign = $_GET['sign'];
        $config = get_addons_config('sf');
        if($sign && $sign == base64_encode(MD5("{$data}&{$config['app_key']}&{$config['app_secret']}"))){
            $data = json_decode($data,true);
            $model = (new OrderModel)->where('order_no',$data['shop_order_id'])->find();
            $model->updateDeliveryStatus($data,'sf');
            return true;
        }else {
            return false;
        }
    }

    /**
     * 达达异步通知处理
     */
    public function dada()
    {
		$data = file_get_contents('php://input');
		$data = json_decode($data,true);
        $model = (new OrderModel)->where('order_no',$data['order_id'])->find();
		$model->updateDeliveryStatus($data,'dada');
        return true;
    }

    /**
     * UU异步通知处理
     */
    public function uu()
    {
        $data = file_get_contents('php://input');
		$data = json_decode($data,true);
		write_log($data,__DIR__);
        $model = (new OrderModel)->where('order_no',$data['originId'])->find();
		$model->updateDeliveryStatus($data,'uu');
        return true;
    }
   
    /**
     * 码科配送异步通知处理
     */
    public function make()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data,true);
        $model = (new OrderModel)->where('order_no',$data['trade_no'])->find();
        $model->updateDeliveryStatus($data,'make');
        return true;
    }
    /**
     * 闪送异步通知处理
     */
    public function shansong()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data,true);
        $model = (new OrderModel)->where('order_no',$data['orderNo'])->find();
        $model->updateDeliveryStatus($data,'shansong');
        return true;
    }
}
