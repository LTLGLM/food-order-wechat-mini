<?php
namespace app\common\model;

use think\facade\Db;

/**
 * 充值记录模型
 */
class RechargeLog extends BaseModel
{
    // 定义表名
    protected $name = 'recharge_log';
    // 定义主键
    protected $pk = 'recharge_log_id';
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
     * 充值方式
     */
    public function getModeAttr($value)
    {
        $status = [10 => '自助', 20 => '后台'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 充值类型
     */
    public function getTypeAttr($value)
    {
        $status = [10 => '余额', 20 => '积分'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取列表
     */
    public function getList(int $user_id = 0,int $type = 10)
    {
        // 筛选条件
        $filter = [
            'type' => $type,
        ];
        $user_id > 0 && $filter['user_id'] = $user_id;
        // 执行查询
        return $this->with(['user'])
            ->where($filter)
            ->order('recharge_log_id','desc')
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    
    /**
     * 添加
     */
    public function add(array $data)
    {
		$data['order_no'] = order_no();//生成订单号
        $user = User::get($data['user_id']);
        if(isset($data['type']) and $data['type'] == 20){
            //积分充值
            $user->score = ['inc',$data["money"]];
        }else{
            //余额充值
            $user->money = ['inc',$data["money"]];
        }
		// 开启事务
        Db::startTrans();
        try {
        	$user->save();
            $this->save($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }
    
    /**
     * 根据条件统计数量
     */
    public static function getCount(int $type = 10)
    {
        // 筛选条件
        $filter = [
            'type' => $type,
        ];
        $count = array();
        //全部
        $count['all'] = self::where($filter)->sum('money');
        //今天
        $star = strtotime(date("Y-m-d"),time());
        $count['today'] = self::where($filter)->where('create_time','>',$star)->sum('money');
        //昨天
        $star = strtotime("-1 day");
        $end = strtotime(date("Y-m-d"),time());
        $count['today2'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        //前天
        $star = strtotime("-2 day");
        $end = strtotime("-1 day");
        $count['today3'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        //-4天
        $star = strtotime("-3 day");
        $end = strtotime("-2 day");
        $count['today4'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        //-5天
        $star = strtotime("-4 day");
        $end = strtotime("-3 day");
        $count['today5'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        //-6天
        $star = strtotime("-5 day");
        $end = strtotime("-4 day");
        $count['today6'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        //-7天
        $star = strtotime("-6 day");
        $end = strtotime("-5 day");
        $count['today7'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        
        //本月起至时间 - 月度统计 
        $end = mktime(0,0,0,date('m'),1,date('y'));
        $count['month'] = self::where($filter)->where('create_time','>',$end)->sum('money');
        //上月开始  
        $star = mktime(0,0,0,date('m')-1,1,date('y'));
        $count['month2'] = self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money');
        return $count;
    }

    /**
     * 根据时间段统计数量
     */
    public static function getDateCount(array $data, int $type = 10)
    {
        // 筛选条件
        $filter = [
            'type' => $type,
        ];
        $count = self::where($filter)->where('create_time','>',$data['star'])->where('create_time','<',$data['end'])->sum('money');
        return $count;
    }
    
    
}