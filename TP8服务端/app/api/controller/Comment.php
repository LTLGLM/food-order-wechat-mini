<?php
namespace app\api\controller;

use app\api\model\Comment as CommentModel;

/**
 * 订单评论
 */
class Comment extends Controller
{
    /**
     * 门店列表
     */
    public function lists()
    {
        $model = new CommentModel;
        $list = $model->getList(0,1);
        return $this->renderSuccess($list);
    }
}
