<?php
namespace app\common\model;
use hema\wechat\Wxapp;

/**
 * 预约订桌模型
 */
class Pact extends BaseModel
{
    // 定义表名
    protected $name = 'pact';
    // 定义主键
    protected $pk = 'pact_id';
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
     * 约定时间
     */
    public function getPactTimeAttr($value)
    {
        return ['date' => date("Y-m-d H:i:s",$value),'text' => (int)date("m",$value) .'月'. (int)date("d",$value) . '日' . date("H:i",$value), 'value' => $value];
    }
    
    /**
     * 状态
     */
    public function getStatusAttr($value,$data)
    {
        if($data['pact_time'] < time()){
            $this->where('pact_id',$data['pact_id'])->where('status',10)->update(['status' => 20]);
            $value = 20;
        }
        $status = [10 => '预约中', 20 => '已过期', 30 => '已完成', 40 => '已取消', 50 => '已同意'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取列表
     */
    public function getList($status = 0,$search='',$user_id=0)
    {
        $model = $this->with(['user']);
        //筛选
        $filter = [];
        $status > 0 && $filter['status'] = $status;
        $user_id > 0 && $filter['user_id'] = $user_id;
        $search = trim($search);
        if(!empty($search)){
            //是否是手机号
            if(is_phone($search)){   
                $filter['phone'] = $search;
            }else{ 
                $model->where('linkman','like',"%{$search}%");
            }
        }
        // 排序规则
        $sort = [];
        $sort = ['pact_time' => 'desc'];//按照约定时间排序
        // 执行查询
        return $model->where($filter)
            ->order($sort)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    /**
     * 获取详情
     */
    public static function detail($id)
    {
        return self::with(['user'])->find($id);
    }

    /**
     * 状态操作
     */
    public function status($status)
    {
        //获取订阅消息配置
        $template_id = trim(Setting::getItem('wxapptpl')['pact_status']);
        if(!empty($template_id)){
            $arr = [10 => '预约中', 20 => '已过期', 30 => '已完成', 40 => '已取消', 50 => '已同意'];
            $data = [
                'name1' => [
                    'value' => $this->linkman //姓名
                ],
                'phone_number2' => [
                    'value' => $this->phone //电话
                ],
                'time3' => [
                    'value' =>  date('Y-m-d H:i',$this->pact_time['value'])//预约时间
                ],
                'phrase5' => [
                    'value' =>  $arr[$status] //预约状态
                ]
            ];
            
            $wx = new Wxapp;
            $queryarr = [
                'touser' => $this['user']['open_id'],
                'template_id' => $template_id,
                'page' => 'pages/user/pact/index',
                'data' => $data
            ];
            $wx->sendMessage($queryarr);
        }
        return $this->save(['status' => $status]) !== false;
    }
    
     /**
     * 根据条件统计数量
     */
    public static function getCount()
    {
        $self = new static;
        $count = array();
        //全部
        $count['all'] = self::count();//订单总数
        $count['now'] = self::where('status',10)->count();//进行中预约订单
        //今天
        $star = strtotime(date("Y-m-d"),time());
        $count['today'] = self::where('create_time','>',$star)->count();
        //昨天
        $star = strtotime("-1 day");
        $end = strtotime(date("Y-m-d"),time());
        $count['today2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();
        //本月起至时间 - 月度统计 
        $end = mktime(0,0,0,date('m'),1,date('y')); 
        $count['month'] = self::where('create_time','>',$end)->count();
        //上月开始  
        $star = mktime(0,0,0,date('m')-1,1,date('y'));
        $count['month2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();  
        return $count;
    }

}