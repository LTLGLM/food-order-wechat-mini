<?php
namespace app\api\controller;

use hema\wechat\Pay as Wxpay;
use hema\alipay\Driver as AlipayPay;
use app\api\model\Order as OrderModel;
use app\api\model\User as UserModel;
use app\api\model\RechargeLog as RechargeLogModel;
use app\api\model\PaybillLog as PaybillLogModel;

/**
 * 支付异步通知接口
 */
class Notify
{
    /**
     * 订单支付
     */
    public function order($mode='weixin')
    {
        if($mode == 'weixin'){
            $pay = new Wxpay; 
        }else{
            $pay = new AlipayPay($mode); 
        }
        $pay->notify(new OrderModel);
    }
    /**
     * 订单退款
     */
    public function orderRefund($mode='weixin')
    {
        if($mode == 'weixin'){
            $pay = new Wxpay; 
        }else{
            $pay = new AlipayPay($mode); 
        }
        $pay->refundsNotify(new OrderModel);
    }
    
    /**
     * 购买会员卡
     */
    public function buyvip($mode='weixin')
    {
        if($mode == 'weixin'){
            $pay = new Wxpay; 
        }else{
            $pay = new AlipayPay($mode); 
        }
        $pay->notify(new UserModel,'add');
    }
    
    /**
     * 线上买单
     */
    public function paybill($mode='weixin')
    {
        if($mode == 'weixin'){
            $pay = new Wxpay; 
        }else{
            $pay = new AlipayPay($mode); 
        }
        $pay->notify(new PaybillLogModel,'add');
    }
    
    /**
     * 会员充值
     */
    public function recharge($mode='weixin')
    {
        if($mode == 'weixin'){
            $pay = new Wxpay; 
        }else{
            $pay = new AlipayPay($mode); 
        }
        $pay->notify(new RechargeLogModel,'add');
    }
}
