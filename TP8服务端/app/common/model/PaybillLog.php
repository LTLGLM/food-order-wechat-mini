<?php
namespace app\common\model;

/**
 * 线上买单记录模型
 */
class PaybillLog extends BaseModel
{
    // 定义表名
    protected $name = 'paybill_log';
    // 定义主键
    protected $pk = 'paybill_log_id';
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
     * 支付方式
     */
    public function getPayModeAttr($value)
    {
        $status = [1 => '微信', 4 => '支付宝'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 获取列表
     */
    public function getList($user_id=0)
    {
        $filter = [];
        $user_id > 0 && $filter['user_id'] = $user_id;
        // 执行查询
        return $this->with(['user'])
            ->where($filter)
            ->order('recharge_log_id','desc')
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    
    /**
     * 根据条件统计数量
     */
    public static function getCount()
    {
        // 筛选条件
        $filter = [];
        $count = array();
        //全部
        $count['all'] = [
            'all' => self::where($filter)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->sum('money'),
        ];
        //今天
        $star = strtotime(date("Y-m-d"),time());
        $count['today'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->sum('money'),
        ];
        //昨天
        $star = strtotime("-1 day");
        $end = strtotime(date("Y-m-d"),time());
        $count['today2'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        //前天
        $star = strtotime("-2 day");
        $end = strtotime("-1 day");
        $count['today3'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        //-4天
        $star = strtotime("-3 day");
        $end = strtotime("-2 day");
        $count['today4'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        //-5天
        $star = strtotime("-4 day");
        $end = strtotime("-3 day");
        $count['today5'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        //-6天
        $star = strtotime("-5 day");
        $end = strtotime("-4 day");
        $count['today6'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        //-7天
        $star = strtotime("-6 day");
        $end = strtotime("-5 day");
        $count['today7'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        
        //本月起至时间 - 月度统计 
        $end = mktime(0,0,0,date('m'),1,date('y'));
        $count['month'] = [
            'all' => self::where($filter)->where('create_time','>',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$end)->sum('money'),
        ];
        //上月开始  
        $star = mktime(0,0,0,date('m')-1,1,date('y'));
        $count['month2'] = [
            'all' => self::where($filter)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$star)->where('create_time','<',$end)->sum('money'),
        ];
        return $count;
    }

    /**
     * 根据时间段统计数量
     */
    public static function getDateCount(array $data)
    {
        // 筛选条件
        $filter = [];
        $count = [
            'all' => self::where($filter)->where('create_time','>',$data['star'])->where('create_time','<',$data['end'])->sum('money'),
            'weixin' => self::where($filter)->where('pay_mode',1)->where('create_time','>',$data['star'])->where('create_time','<',$data['end'])->sum('money'),
            'alipay' => self::where($filter)->where('pay_mode',4)->where('create_time','>',$data['star'])->where('create_time','<',$data['end'])->sum('money'),
        ];
        return $count;
    }
    
}