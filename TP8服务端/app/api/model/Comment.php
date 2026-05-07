<?php
namespace app\api\model;

use app\common\model\Comment as CommentModel;
use think\facade\Db;

/**
 * 订单评论模型
 */
class Comment extends CommentModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [];
    
    /**
     * 添加
     */
    public function add(array $data)
    {
		$model = Order::get($data['order_id']);
    	// 开启事务
        Db::startTrans();
        try {
    		$model->save(['is_cmt' => 1]);
			$this->save($data);
		 	Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;
    }
}

