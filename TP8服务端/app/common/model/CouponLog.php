<?php
namespace app\common\model;

use think\facade\Db;

/**
 * 发券记录模型
 */
class CouponLog extends BaseModel
{
    // 定义表名
    protected $name = 'coupon_log';
    // 定义主键
    protected $pk = 'coupon_log_id';
    // 追加字段
    protected $append = [];

    /**
     * 关联优惠券表
     */
    public function coupon()
    {
        return $this->belongsTo('app\\common\\model\\Coupon','coupon_id');
    }
    
    /**
     * 获取列表
     */
    public function getList()
    {
        // 筛选条件
        $filter = [];
        // 排序规则
        $sort = [];
        $sort = ['coupon_log_id' => 'desc'];//按照时间排序
        // 执行查询
        return $this->with(['coupon'])
            ->where($filter)
            ->order($sort)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    /**
     * 添加
     */
    public function add(array $data)
    {
        //获取优惠券详情
        $coupon = Coupon::get($data['coupon_id']);
        //获取合法用户的id
        $filter = [];
        $userId = User::order('user_id','desc')->column('user_id');
        //计算发送数量
        $data['count'] = count($userId);
        //起始时间计算
        //如果是数字
        if(is_numeric($data['expire_star'])){
            $star = strtotime(date('Y-m-d').' 00:00:00');//获取今天的起始时间戳
            //如果数字大于0
            if($data['expire_star'] > 0){
                $expire_star = $star + $data['expire_star'] * 3600 * 24;//几天后的起始时间戳
            }else{
               $expire_star = $star;//今天的起始时间戳
            }
        }else{
            //如果是日期
            $expire_star = strtotime($data['expire_star'].' 00:00:00');//计算指定日期的起始时间戳
        }
        
        //结束时间计算
        //如果是数字
        if(is_numeric($data['expire_end'])){
            //如果数字大于0
            if($data['expire_end'] > 0){
                $expire_end = $expire_star + ($data['expire_end'] + 1) * 3600 * 24;//几天后的起始时间戳
            }else{
               $expire_end = $expire_star + 3600*24;//今天的结束时间戳
            }
        }else{
            //如果是日期
            $expire_end = strtotime($data['expire_end'].' 23:59:59');//计算指定日期的结束时间戳
        }
        
        //组成用户批量记录
        $userCoupon = array();
        foreach ($userId as $key => $value) {
            array_push($userCoupon,[
                'coupon_id' => $data['coupon_id'],
                'type' => $coupon['type']['value'],
                'order_price' => $coupon['order_price'],
                'values' => $coupon['values'],
                'user_id' => $value,
                'expire_star' => $expire_star,
                'expire_end' => $expire_end
            ]);
        }
        // 开启事务
        Db::startTrans();
        try {
            // 添加记录
            $this->save($data);
            // 用户发券
            if(sizeof($userCoupon) > 0){
                $couponUser = new CouponUser;
                $couponUser->saveAll($userCoupon);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;  
    }
    
    /**
     * 删除
     */
    public function remove()
    {
        //验证是否有未使用
        return $this->delete(); 
    }
}