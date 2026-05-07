<?php
namespace app\common\model;

use think\facade\Session;
use hema\wechat\Wechat as Wx;

/**
 * 用户模型
 */
class User extends BaseModel
{
    // 定义表名
    protected $name = 'user';
    // 定义主键
    protected $pk = 'user_id';
    // 追加字段
    protected $append = ['usable'];
	
    /**
     * 计算可用积分
     */
    public function getUsableAttr($value,$data)
    {
        return $data['score'] - $data['converted'];
    }
    
    /**
     * 关联收货地址表
     */
    public function address()
    {
        return $this->hasMany('app\\common\\model\\Address','user_id','address_id');
    }
    
    /**
     * 关联收货地址表 (默认地址)
     */
    public function addressDefault()
    {
        return $this->belongsTo('app\\common\\model\\Address','address_id');
    }
    
    /**
     * 显示性别
     */
    public function getGenderAttr($value)
    {
        $status = [0 => '未知', 1 => '先生', 2 => '女士'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 用户头像
     */
    public function getAvatarAttr($value)
    {
        if(empty($value)){
            $url = base_url() . 'assets/img/avatar.png';
        }else{
            $url = uploads_url() . $value;
        }
        return ['url' => $url, 'path' => $value];
    }
    
    /**
     * 显示等级
     */
    public function getVAttr($value,$data)
    {
        $grade = Setting::getItem('vip');
        //是否开启会员
        if($value > 0){
            //判断当前会员积分是否大于当前等级
            if($data['score'] >= $grade['vip'][$value-1]['score']){
                //不是最高等级6可进行-升级操作
                if($value < 6){
                    $value = $value + 1;//等级加1
                    $user = self::get($data['user_id']);
                    $user->v = $value;//升级
                    $user->save();
                }
                //升级任务是否赠送优惠券
                $gift = $grade['vip'][$value-1]['gift'];
                if(sizeof($gift) > 0){
                    $add_coupon = [];
                    $model = new CouponUser;
                    foreach ($gift as $item){
                        $add_coupon[] = $model->dateTmp($item,$data['user_id']);
                    }
                    //判断是否有赠送优惠券
                    if(sizeof($add_coupon) > 0){
                        $model->saveAll($add_coupon);//添加多条记录
                    }
                }
            }
            $grade['lving'] = 100;//据升下一级完成百分比（初始100%）
            $grade['lack'] = 0;//升下一级所需积分（初始0）
            //如果已经是最高级别
            if($value == 6){
               $grade['upper'] = $grade['vip'][$value-1]['score']; //下一级所需积分总额
            }else{
                $grade['upper'] = $grade['vip'][$value]['score'];//下一级所需积分总额
                $m = $grade['upper'] - $grade['vip'][$value-1]['score']; //计算升级还需要得积分
                $x = $data['score'] - $grade['vip'][$value-1]['score'];//计算出超过当前等级的积分
                $grade['lack'] = $m - $x;//计算升到下一级还需要多少积分
                $grade['lving'] = round($x/$m*100); //计算已经完成百分比
            }
            return ['value' => $value,'text' => $grade['vip'][$value-1]['name'],'grade' => $grade];
        }
        return ['value' => $value,'text' => '普通用户','grade' => $grade];
    }
    
    /**
     * 获取列表
     */
    public function getList($search = '',$gender = 'all')
    {
        $model = $this->order('user_id','desc');
        $filter = [];
        $gender != 'all' && $filter['gender'] = $gender;
        $search = trim($search);
        if(!empty($search)){
            //是否是数字
            if(is_numeric($search)){   
                //是否是手机号
                if(is_phone($search)){
                    $filter['phone'] = $search;
                }else{
                    $filter['user_id'] = $search;
                }
            }else{
               //不是数字   
                $model->where('nickname','like',"%{$search}%");
            }
        }
        // 执行查询
        return $model->where($filter)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }

    /**
     * 修改
     */
    public function edit(array $data)
    {
        return $this->save($data) !== false;
    }
    
    /**
	 * 关注公众号
	 */
	public function subscribe(array $data)
	{
	    $wx = new Wx;
	    if($result = $wx->getUserInfo($data['FromUserName'])){
	        $userInfo = [
	            'nickname' => '微信用户',
                'wx_open_id' => $result['openid'],
                'is_wechat' => 1,
            ];
            //判断是否 开启UnionID机制
	        isset($result['unionid']) && $userInfo['union_id'] = $result['unionid'];
	        
	        if(!$wxUser = $this->where('wx_open_id',$result['openid'])->find()){
	            $wxUser = $this;
	        }
    	    return $wxUser->save($userInfo);
	    }
	    return false;
	}
	
	/**
	 * 取消关注公众号
	 */
	public function unsubscribe(array $data)
	{
	    if($wxUser = $this->where('wx_open_id',$data['FromUserName'])->find()){
            $wxUser->is_wechat = 0;
            return $wxUser->save();
        }
        return false;
	}
    
    /**
     * 粉丝转用户
     */
    public function syncFans()
    {
        $wx = new Wx;
        if(!$result = $wx->getUserList()){
            return $this->renderError($wx->getError());
        }
        $openId = $result['data']['openid'];
        $arr = [];
        //筛选不存在得粉丝会员
        foreach ($openId as $item){
            if($user = $this->where('wx_open_id',$item)->find()){
                //已存在，判断是否关注
                if($user['is_wechat'] == 0){
                    $user->is_wechat = 1;
                    $user->save();
                }
            }else{
                $arr[] = [
                    'nickname' => '微信用户',
                    'wx_open_id' => $item
                ];
            }
        }
        if(sizeof($arr) > 0){
            return $this->saveAll($arr);
        }
        return true;
        
    }
    
    /**
     * 根据条件统计数量
     */
    public static function getCount()
    {
        $self = new static;
        $count = array();
        
        //全部统计
        $count['all'] = [
            'all' => self::count(),
            'money' => self::sum('money'),//余额
        ];
        //今天统计
        $star = strtotime(date('Y-m-d 00:00:00',time()));
        $count['today'] = self::where('create_time','>',$star)->count();
        //昨天统计
        $star = strtotime("-1 day");
        $end = strtotime(date('Y-m-d 00:00:00',time()));
        $count['today2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //前天统计
        $star = strtotime("-2 day");
        $end = strtotime("-1 day");
        $count['today3'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //-4天统计
        $star = strtotime("-3 day");
        $end = strtotime("-2 day");
        $count['today4'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //-5天统计
        $star = strtotime("-4 day");
        $end = strtotime("-3 day");
        $count['today5'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //-6天统计
        $star = strtotime("-5 day");
        $end = strtotime("-4 day");
        $count['today6'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //-7天统计
        $star = strtotime("-6 day");
        $end = strtotime("-5 day");
        $count['today7'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        //本月统计 
        $end = mktime(0,0,0,date('m'),1,date('y')); 
        $count['month'] = self::where('create_time','>',$end)->count();//全部数量
        //上月统计  
        $star = mktime(0,0,0,date('m')-1,1,date('y'));
        $count['month2'] = self::where('create_time','>',$star)->where('create_time','<',$end)->count();//全部数量
        return $count;
    }
}