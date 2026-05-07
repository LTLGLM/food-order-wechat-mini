<?php
namespace app\common\model;

/**
 * 用户充值套餐模型
 */
class RechargePlan extends BaseModel
{
    // 定义表名
    protected $name = 'recharge_plan';
    // 定义主键
    protected $pk = 'recharge_plan_id';
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
     * 赠品类型
     */
    public function getGiftTypeAttr($value)
    {
        $status = [10 =>'优惠券', 20 => '余额'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取列表
     */
    public function getList($page=true)
    {
        // 执行查询
        if($page){
            return $this->with(['coupon'])
                ->order(['sort' => 'asc','recharge_plan_id'=> 'desc'])
                ->paginate(['list_rows'=>15,'query' => request()->param()]);
        }
        return $this->with(['coupon'])
            ->order(['sort' => 'asc','recharge_plan_id'=> 'desc'])
            ->select();
    }

    /**
     * 新增记录
     */
    public function add(array $data)
    {
        return $this->save($data);
    }
    
    /**
     * 更新记录
     */
    public function edit(array $data)
    {
        return $this->save($data) !== false;
    }
    
    /**
     * 删除记录
     */
    public function remove() 
    {
        return $this->delete();
    }
}