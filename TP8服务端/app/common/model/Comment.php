<?php
namespace app\common\model;

/**
 * 订单评论模型类
 */
class Comment extends BaseModel
{
    // 定义表名
    protected $name = 'comment';
    // 定义主键
    protected $pk = 'comment_id';
    protected $append = ['total'];
    /**
     * 用户评分
     */
    public function getTotalAttr($value, $data)
    {
        $value = ($data['serve']+$data['speed']+$data['flavor']+$data['ambient'])/4;
        return $value;
    }
    
    /**
     * 关联订单
    */
    public function order()
    {
        return $this->belongsTo('app\\common\\model\\Order','order_id');
    }

     /**
     * 关联用户表
    */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\User','user_id');
    }

    /**
     * 显示状态
     */
    public function getIsShowAttr($value)
    {
        $status = [0 => '隐藏', 1 => '显示'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取列表
    */
    public function getList($user_id = 0, $is_show = -1)
    {
        // 筛选条件
        $filter = [];
        $user_id > 0 && $filter['user_id'] = $user_id;
        $is_show > -1 && $filter['is_show'] = $is_show;
        return $this->with(['order','user'])
            ->where($filter)
            ->order(['comment_id' => 'desc'])
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    
    /**
     * 详情
     */
    public static function detail($id)
    {
        return self::with(['order','user'])->find($id);
    }
    /**
     * 编辑
     */
    public function edit(array $data)
    {
        return $this->save($data) !== false;
    }
    /**
     * 显示状态编辑
     */
    public function status()
    {
        $this->is_show['value']==0 ? $this->is_show = 1 : $this->is_show = 0;
        return $this->save() !== false;
    }
    
    /**
     * 评分计算
     */
    public static function score()
    {
        $serve = self::avg('serve');
        $speed = self::avg('speed');
        $flavor = self::avg('flavor');
        $ambient = self::avg('ambient');
        $serve <=0 && $serve = 5;
        $speed <=0 && $speed = 5;
        $flavor <=0 && $flavor = 5;
        $ambient <=0 && $ambient = 5;
        $all = ($serve+$speed+$flavor+$ambient)/4;
        return [
            'all' => round($all,2),
            'serve' => round($serve,2),
            'speed' => round($speed,2),
            'flavor' => round($flavor,2),
            'ambient' => round($ambient,2)
        ];
    }
    
    /**
     * 根据条件统计数量
     */
    public static function getCount()
    {
        $self = new static;
        
        $count = array();
        //全部
        $count['all'] = self::count();
        //今天
        $star = strtotime(date("Y-m-d"),time());
        $count['today'] = self::where('create_time','>',$star)->count();
        //昨天
        $star = strtotime("-1 day");
        $end = strtotime(date("Y-m-d"),time());
        $count['today2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //前天
        $star = strtotime("-2 day");
        $end = strtotime("-1 day");
        $coun['today3'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //-4天
        $star = strtotime("-3 day");
        $end = strtotime("-2 day");
        $count['today4'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //-5天
        $star = strtotime("-4 day");
        $end = strtotime("-3 day");
        $count['today5'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //-6天
        $star = strtotime("-5 day");
        $end = strtotime("-4 day");
        $count['today6'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //-7天
        $star = strtotime("-6 day");
        $end = strtotime("-5 day");
        $count['today7'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //本月统计 
        $end = mktime(0,0,0,date('m'),1,date('y')); 
        $count['month'] = self::where('create_time','>',$end)->count();//全部数量
        //上月统计  
        $star = mktime(0,0,0,date('m')-1,1,date('y'));
        $count['month2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        
        return $count;
    }
}