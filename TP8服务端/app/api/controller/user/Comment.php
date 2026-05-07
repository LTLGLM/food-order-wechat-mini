<?php
namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\Comment as CommentModel;

/**
 * 订单评论
 */
class Comment extends Controller
{
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUserDetail();   // 用户信息
    }

    /**
     * 我的列表
     */
    public function lists()
    {
        $model = new CommentModel;
        $list = $model->getList($this->user_id);
        return $this->renderSuccess($list);
    }

    /**
     * 添加评论
     */
    public function add()
    {
        $model = new CommentModel;
		if($model->add($this->request->post())){
			return $this->renderMsg('操作成功');
		}
		$error = $model->getError() ?: '操作失败';
		return $this->renderError($error);
    }
}
