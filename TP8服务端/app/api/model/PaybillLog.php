<?php
namespace app\api\model;

use app\common\model\PaybillLog as PaybillLogModel;
use think\facade\Cache;
use think\facade\Db;

/**
 * 线上买单记录模型
 */
class PaybillLog extends PaybillLogModel
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
        /*
        if($mp == 'weixin'){
		    $pay_mode = 1;//微信
		}else{
		    $pay_mode = 4;//支付宝
		}
		*/
        $data['transaction_id'] = $transaction_id;
        // 开启事务
        Db::startTrans();
        try {
            //更新用户余额信息
    		$user = User::get($data['user_id']);
    		$user->pay = ['inc', $data['money']];//增加消费金额
    		//开通会员才有积分
            if($user['v']['value'] > 0){
                $score = intval($data['money']);
                $user->score = ['inc', $score];//增加消费积分
            }
    		$user->save();
    		//添加交易记录
            $this->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }
}
