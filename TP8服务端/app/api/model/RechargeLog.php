<?php
namespace app\api\model;

use app\common\model\RechargeLog as RechargeLogModel;
use think\facade\Cache;
use think\facade\Db;

/**
 * 充值记录模型
 */
class RechargeLog extends RechargeLogModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [];
    
    /**
     * 获取充值订单详情 - 微信充值回调
     */
    public function payDetail(string $order_no)
    {
        return Cache::get($order_no);
    }

    /**
     * 更新充值付款状态 - 微信充值回调
     */
    public function updatePayStatus($mp,string $transaction_id, array $data)
    {
		$data['transaction_id'] = $transaction_id;
        $gift_money = 0;//赠送金额
        $coupon = null;//赠送优惠券
        $model = new CouponUser;
        
        //判断是否赠送套餐
        if(isset($data['recharge_plan_id']) AND $data['recharge_plan_id'] > 0){
            if($plan = RechargePlan::get($data['recharge_plan_id'],['coupon'])){
                //判断套餐是不是赠送优惠券
                if($plan['gift_type']['value'] == 10){
                    //优惠券
                    $coupon = $model->dateTmp($plan['coupon'],$data['user_id']);
                }else{
                    //赠送余额
                    $gift_money = $plan['gift_money']; //赠送现金余额
                }
            }
        }
        $data['give_money'] = $gift_money;
        // 开启事务
        Db::startTrans();
        try {
            //更新用户余额信息
    		$user = User::get($data['user_id']);
    		$user->money = ['inc', $data['money']+$gift_money];//增加余额
    		$user->save();
    		//添加交易记录
            //是否有赠送优惠券
            if(!is_null($coupon)){
                $model->save($coupon);
            }
            $this->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }
}
