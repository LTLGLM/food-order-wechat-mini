<?php
namespace app\common\model;

/**
 * 签到记录模型
 */
class SignLog extends BaseModel
{
    // 定义表名
    protected $name = 'sign_log';
    // 定义主键
    protected $pk = 'sign_log_id';
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
     * 奖励类型
     */
    public function getTypeAttr($value)
    {
        $status = ['score' => '积分', 'money' => '余额'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取列表
     */
    public function getList($user_id = 0)
    {
        // 筛选条件
        $filter = [];
        $user_id > 0 && $filter['user_id'] = $user_id;
        // 执行查询
        return $this->with(['user'])
            ->where($filter)
            ->order('sign_log_id','desc')
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
}