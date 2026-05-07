<?php
namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Order as OrderModel;
use hema\wechat\Pay as WxPay;
use hema\alipay\Driver as AlipayPay;
use think\facade\Cache;

/**
 * 用户订单管理
 */
class Order extends Controller
{
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUserDetail();   // 用户信息
    }

    /**
     * 我的所有类型订单列表
     */
    public function all()
    {
        $model = new OrderModel;
        $list = [
            0 => $model->getList('today',$this->user_id),
            1 => $model->getList('old',$this->user_id),
        ];
        return $this->renderSuccess($list);
    }

    /**
     * 我的订单列表
     */
    public function lists(string $dataType)
    {
        $model = new OrderModel;
        $list = $model->getList($dataType,$this->user_id);
        return $this->renderSuccess($list);
    }

    /**
     * 订单详情信息
     */
    public function detail($order_id)
    {
        $order = OrderModel::detail($order_id);
        return $this->renderSuccess($order);
    }

    /**
     * 取消订单
     */
    public function cancel($order_id)
    {
        $model = OrderModel::get($order_id);
        if ($model->cancel()) {
            return $this->renderMsg('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 确认收货
     */
    public function receipt($order_id)
    {
        $model = OrderModel::get($order_id);
        if ($model->receipt()) {
            return $this->renderMsg('操作成功');
        }
		$error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }

    /**
     * 立即支付
	 * $pay_mode 支付模式 1微信，2余额,3面对面，4支付宝
	 * $browser 浏览器类型，只有H5才会传递
     */
    public function pay($order_id, $pay_mode,$browser='')
    {
        // 订单详情
        $order = OrderModel::detail($order_id);
        if($data = $order->userPay($pay_mode,$this->mp,$browser,order_no())){
            return $this->renderSuccess($data);
        }
        $error = $order->getError() ?: '支付失败';
        return $this->renderError($error);
    }

     /**
     * 线上买单
     * $pay_mode 1=微信 4=支付宝
     * $browser 浏览器类型，只有H5才会传递
     */
    public function paybill($money,$pay_mode,$notes = '线上买单',$browser='')
    {
        $order_no = order_no();
        $order = [
            'pay_mode' => $pay_mode,//支付类型
            'order_no' => $order_no,
            'money' => $money,
            'remark' => $notes,
            'user_id' => $this->user_id,
        ];
        Cache::set($order_no, $order,7200);
        $mp = $this->mp;
        //微信支付
        if($pay_mode == 1){
            $wx = new WxPay;
            //网页其它浏览器平台支付接口
            if($mp == 'h5' and $browser == 'other'){
                if(!$result = $wx->h5($order_no,$money,'api/notify/paybill','线上买单')){
                    return $this->renderError($wx->getError());
                }
            }
            //微信小程序或微信浏览器支付接口
            if($mp == 'weixin' or ($mp == 'h5' and $browser == 'weixin')){
                if(!$result = $wx->jsapi($order_no,$money,$this->user['open_id'],'api/notify/paybill','线上买单')){
                    return $this->renderError($wx->getError());
                }
            }
        }
        //支付宝支付
        if($pay_mode == 4){
            //非微信浏览器支付接口
            if($mp == 'h5' and $browser != 'weixin'){
                $alipay = new AlipayPay('aliweb');
                if(!$result = $alipay->tradeWapPay($order_no,$money,'api/notify/paybill/mode/aliweb','h5/#/pages/index/index','线上买单')){
                    $this->error = $alipay->getError();
                    return false;
                }
            }
            //支付宝小程序支付接口
            if($mp == 'alipay'){
                $alipay = new AlipayPay('aliapp');
                if(!$result = $alipay->tradeCreate($order_no,$money,$this->user['alipay_open_id'],'api/notify/paybill/mode/aliapp','线上买单')){
                    $this->error = $alipay->getError();
                    return false;
                }
            }
        }
        return $this->renderSuccess([
            'payment' => $result
        ]);
    }
}
