<?php
namespace app\api\model;

use app\common\model\Pact as PactModel;

/**
 * 预约模型
 */
class Pact extends PactModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [];
    
     /**
     * 添加
     */
    public function add(array $data)
    {
        $pact = Setting::getItem('pact');
        //判断是否开启预约
        if($pact['is_open'] == 0){
            $this->error = '该门店未开启在线预约';
            return false;
        }
        $end_time = strtotime(date('Y-m-d') . ' 23:59:59');//计算今天的结束时间
        $end_time = $end_time + (int)$pact['range'] * 3600 * 24;//计算可提前预约的天数时间戳
        
        //判断是否在指定预约时间段
        if($data['pact_time'] > $end_time){
            $this->error = '超出时间范围：只能提前'.$pact['range'].'天预约';
            return false;
        }
        //是否开启打印预约订单
        if($pact['is_print'] == 1){
            Device::print($data,3);//打印预约订单订单
        }
		return $this->save($data);
    }
    
    /**
     * 删除
     */
    public function remove()
    {
        return $this->delete();
    }
}
