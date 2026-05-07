<?php
namespace app\api\model;

use app\common\model\SignLog as SignLogModel;
use think\facade\Cache;
use think\facade\Db;

/**
 * 签到记录模型
 */
class SignLog extends SignLogModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [];
    
    /**
     * 添加
     */
    public function add($user_id)
    {
        $new_data[0]['user_id'] = $user_id;
        $sign = Setting::getItem('sign');//获取签到配置
        $user = User::get($user_id);//获取用户信息
        //判断奖励数值
        if($sign['mode'] == 'fixed'){
            $new_data[0]['number'] = $sign['number'];//固定值
        }else{
            $new_data[0]['number'] = rand(1,$sign['number']);//随机值
        }
        //判断奖励类型
        if($sign['type'] == 'score'){
            $user->score = ['inc', $new_data[0]['number']];//用户积分新增
            $new_data[0]['type'] = 'score';
        }else{
            $user->money = ['inc', $new_data[0]['number']];//用户余额新增
            $new_data[0]['type'] = 'money';
        }
        $new_data[0]['remark'] = '每日签到';
        
        //如果有签到任务
        if(isset($sign['plan']) and sizeof($sign['plan']) > 0){
            $plan = array_sort($sign['plan'], 'days');//按照天数从小到大排序
            $days = Cache::get('sign_' . $user_id,0);
            $days = $days + 1;
            $expire_time = strtotime(date('Y-m-d',strtotime('+1 day'))) - time() + (24*3600);//到期时间第二天24点
            Cache::set('sign_' . $user_id,$days,$expire_time);
            for($n=0;$n<sizeof($plan);$n++){
                if($plan[$n]['days'] == $days){
                    $new_data[1]['user_id'] = $user_id;
                    //判断奖励类型
                    if($plan[$n]['type'] == 'score'){
                        $user->score = ['inc', $plan[$n]['number']];
                        $new_data[1]['type'] = 'score';
                    }else{
                        $user->money = ['inc', $plan[$n]['number']];
                        $new_data[1]['type'] = 'money';
                    }
                    $new_data[1]['number'] = $plan[$n]['number'];
                    $new_data[1]['remark'] = '任务奖励';
                    //判断是否完成最后一个签到任务
                    if(($n+1) == sizeof($plan)){
                        Cache::delete('sign_' . $user_id);
                    }
                    break;
                }
            }
        }

        // 开启事务
        Db::startTrans();
        try {
            // 添加记录
            $this->saveAll($new_data);
            $user->save();//更新用户信息
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }

        return false;  

    }
    
    /**
     * 获取今天签到详情
     */
    public function getIsSign($user_id)
    {
        $beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));//今天开始的时间戳
        return $this->where('user_id',$user_id)->where('create_time','>',$beginToday)->count();
    }
}
