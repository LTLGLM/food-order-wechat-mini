<?php
namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\CouponUser as CouponUserModel;

/**
 * 用户优惠券控制器
 */
class Coupon extends Controller
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
     * 优惠券列表
     */
    public function lists()
    {
        $model = new CouponUserModel;
        $list = $model->getList($this->user_id);
        if($list->toArray()){
            $list = array_repeat($list->toArray(),'coupon_id'); //去除重复的优惠券
        }
        return $this->renderSuccess($list);
    }

    /**
     * 优惠券首页
     */
    public function all()
    {
        $model = new CouponUserModel;
        $list = [
            0 => $model->getList($this->user_id,0,false),
            1 => $model->getList($this->user_id,10,false),
            2 => $model->getList($this->user_id,20,false),
            3 => $model->getList($this->user_id,30,false),
            4 => $model->getList($this->user_id,40,false),
        ];
        $couponCount = CouponUserModel::getUserCount($this->user_id);
        $tabs = [
            ['name' => '全部','badge' => ['value' => $couponCount['all']]],
            ['name' => '现金券','badge' => ['value' => $couponCount['10']]],
            ['name' => '折扣券','badge' => ['value' => $couponCount['20']]],
            ['name' => '赠送券','badge' => ['value' => $couponCount['30']]],
            ['name' => '单品券','badge' => ['value' => $couponCount['40']]],
        ];
        return $this->renderSuccess(compact('list','tabs'));
    }
}
