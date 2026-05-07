<?php
namespace app\common\model;

/**
 * 优惠券模型
 */
class Coupon extends BaseModel
{
    // 定义表名
    protected $name = 'coupon';
    // 定义主键
    protected $pk = 'coupon_id';
    // 追加字段
    protected $append = [];
    
    /**
     * 获取器: 转义数组格式
     */
    public function getGiftAttr($value,$data)
    {
        $temp = json_decode($value, true);
        $data = [];
        foreach ($temp as $item){
           $data[] = json_decode($item, true);
        }
        return $data;
    }

    /**
     * 修改器: 转义成json格式
     */
    public function setGiftAttr($value)
    {
        return json_encode($value,JSON_UNESCAPED_UNICODE);
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
     */
    public function getList($page=true)
    {
        // 筛选条件
        $filter = [];
        // 排序规则
        $sort = ['coupon_id' => 'desc'];
        // 执行查询
        if($page){
            return $this->where($filter)
                ->order($sort)
                ->paginate(['list_rows'=>15,'query' => request()->param()]);;
        }
        return $this->where($filter)
            ->order($sort)
            ->select();
    }

    /**
     * 添加
     */
    public function add(array $data)
    {
        if(!isset($data['gift'])){
           $data['gift'] = [];
        }
        //验证是否选择赠品
        if($data['type'] == 30 or $data['type'] == 40){
            if(sizeof($data['gift'])==0){
                $this->error = '请指定赠品或单品';
                return false;
            }
        }
        return $this->save($data);    
    }
    
    /**
     * 编辑
     */
    public function edit(array $data)
    {
        return $this->save($data) !== false;
    }
    
    /**
     * 删除
     */
    public function remove()
    {
        //验证是否有未使用
        if ($userCoupon = (new CouponUser)->where(['coupon_id' => $this->coupon_id])->count()) {
            $this->error = '用户卡包中有' . $userCoupon . '张未使用，不允许删除';
            return false;
        }
        //验证充值套餐赠品中是否存在该优惠券
        if ($planCoupon = (new RechargePlan)->where(['coupon_id' => $this->coupon_id])->count()) {
            $this->error = '充值套餐中设置为了赠品，需取消该赠品后才可以删除';
            return false;
        }
        //验证会员升级赠品中是否存在该优惠券
        if($grade = Setting::getItem('vip')){
            foreach ($grade as $vo) {
               if(isset($vo['gift'])){
                    foreach ($vo['gift'] as $item) {
                        if($item['coupon_id'] == $this->coupon_id){
                            $this->error = '会员升级中设置为了赠品，需取消该赠品后才可以删除';
                            return false;
                        }
                    }
               }
            }
        }
        return $this->delete(); 
    }
}