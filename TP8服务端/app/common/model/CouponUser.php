<?php
namespace app\common\model;

use hema\Helper;

/**
 * 优惠券用户领取模型
 */
class CouponUser extends BaseModel
{
    // 定义表名
    protected $name = 'coupon_user';
    // 定义主键
    protected $pk = 'coupon_user_id';
    // 追加字段
    protected $append = [];
    
    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\User','user_id');
    }
    /**
     * 关联优惠券表
     */
    public function coupon()
    {
        return $this->belongsTo('app\\common\\model\\Coupon','coupon_id');
    }
    
    /**
     * 有效起始时间
     */
    public function getExpireStarAttr($value,$data)
    {
        return ['text' => date('Y-m-d',$data['expire_star']), 'value' => $value];
    }
    
    /**
     * 有效结束时间
     */
    public function getExpireEndAttr($value,$data)
    {
        return ['text' => date('Y-m-d',$data['expire_end']), 'value' => $value];
    }
    
    /**
     * 类型
     */
    public function getTypeAttr($value)
    {
        $status = [10 => '现金券', 20 => '折扣券', 30 => '赠送券', 40 => '单品券'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 获取列表
     * $valid 是否有效可以使用的
     */
    public function getList($user_id = 0, $type = 0,$valid=true)
    {
        //删除3分钟后过期的优惠券
        $this->where('status',10)->where('expire_end','<',time()-180)->delete();
        $model = $this->with(['coupon','user']);
        if($valid){
            //有效可以使用的
            $model->where('expire_star','<',time())->where('expire_end','>',time());
        }else{
            //所有得
            $model->where('expire_end','>',time());
        }
        //筛选
        $filter = [
            'status' => 10    
        ];
        $user_id > 0 && $filter['user_id'] = $user_id;
        $type > 0 && $filter['type'] = $type;
        // 排序规则
        $sort = [];
        $sort = ['expire_end' => 'asc','coupon_user_id' => 'desc'];//按照时间排序
        // 执行查询
        return $model->where($filter)->order($sort)->select()->toArray();
    }
    
    /**
     * 获取可用的优惠券列表
     */
    public static function checkCoupon(int $user_id, array $order)
    {
        //筛选
        $filter = [
            'user_id' => $user_id,
            'status' => 10,
        ];
        // 排序规则
        $sort = [];
        $sort = ['expire_end' => 'asc','coupon_user_id' => 'desc'];//按照时间排序
        $coupon =  self::with(['coupon'])
            ->where($filter)
            ->where('order_price','>=',$order['order_pay_price'])
            ->where('expire_star','<',time())
            ->where('expire_end','>',time())
            ->order($sort)
            ->select()
            ->toArray();
        $list = [];
        $time_star = strtotime('today');//今天得起始时间戳
        $time_end = strtotime('tomorrow');//今天得结束时间戳
        foreach ($coupon as $item){
            //获取该券今天使用了几张
            $useCount = self::where([
                'user_id' => $user_id,
                'status' => 20,
                'coupon_id' => $item['coupon_id'],
            ])
            ->where('update_time','>',$time_star)
            ->where('update_time','<',$time_end)
            ->count();
            //验证今天是否超过每天得使用数量限制
            if($item['coupon']['use_amount'] == 0 or $useCount < $item['coupon']['use_amount']){
                //如果是单品券
                if($item['type']['value']==40){
                    //验证是否选购了对应的商品
                    foreach ($order['goods_list'] as $goods){
                        //匹配到对应的商品
                        if($goods['goods_id'] == $item['coupon']['gift'][0]['goods_id']){
                            $list[] = $item;
                            break;
                        }
                    }
                }else{
                    $list[] = $item;
                }
            }
        }
        return $list;
    }
    
    /**
     * 使用优惠券
     */
    public function useCoupon(array $order)
    {
        //现金券验证
        if($coupon['type']['value'] == 10){
            $price = (float)$coupon['values'];//优惠金额
            $order['order_pay_price'] = Helper::number2($order['order_pay_price'] - $price);// 实际支付金额
            $order['activity_price'] = Helper::number2($order['activity_price'] + $price); // 优惠金额
        }
        //折扣券
        if($coupon['type']['value'] == 20){
            $price = $order['order_total_price'] - $order['order_total_price'] * (float)$coupon['values'] / 100; //计算优惠金额
            $order['order_pay_price'] = Helper::number2($order['order_pay_price'] - $price);// 实际支付金额
            $order['activity_price'] = Helper::number2($order['activity_price'] + $price); // 优惠金额
        }
        //满赠券
        if($coupon['type']['value'] == 30){
            $order['gift'] = $coupon['coupon']['gift'];
        }
        //单品券
        if($coupon['type']['value'] == 40){
            //循环找到对应的商品
            foreach ($order['goods_list'] as $goods){
                //验证商品是否在购物列表中
                if($goods['goods_id'] == $coupon['coupon']['gift'][0]['goods_id']){
                    $price = $goods['goods_price'] - $goods['goods_price'] * (float)$coupon['values'] / 100; //计算优惠金额
                    $order['order_pay_price'] = Helper::number2($order['order_pay_price'] - $price);// 实际支付金额
                    $order['activity_price'] = Helper::number2($order['activity_price'] + $price); // 优惠金额
                    break;
                }
            }
        }
        
        //优惠后最低价格为0.00
        if($order['order_pay_price'] < 0){
            $order['order_pay_price'] = Helper::number2(0);
        }
        return $order;
    }
    /**
     * 组合数据模板
     */
    public function dateTmp($coupon,$user_id)
    {
        return [
            'coupon_id' => $coupon['coupon_id'],
            'type' => $coupon['type']['value'],
            'order_price' => $coupon['order_price'],
            'values' => $coupon['values'],
            'user_id' => $user_id,
            'expire_star' => time(),//即刻生效
            'expire_end' => time() + 3600*24*$coupon['initial_days'],
        ];
    }
    /**
     * 用户统计数量
     */
    public static function getUserCount($user_id = 0)
    {
        // 筛选条件
        $filter = [];
        $user_id > 0 && $filter['user_id'] = $user_id;
        $count = array();
        $count['all'] = self::where('expire_end','>',time())->where($filter)->count();
        $count['10'] = self::where('expire_end','>',time())->where($filter)->where('type',10)->count();
        $count['20'] = self::where('expire_end','>',time())->where($filter)->where('type',20)->count();
        $count['30'] = self::where('expire_end','>',time())->where($filter)->where('type',30)->count();
        $count['40'] = self::where('expire_end','>',time())->where($filter)->where('type',40)->count();
        return $count;
    }
}