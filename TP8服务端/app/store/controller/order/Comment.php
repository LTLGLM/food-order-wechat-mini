<?php
namespace app\store\controller\order;

use app\store\controller\Controller;
use app\store\model\Comment as CommentModel;
use think\facade\View;

/**
 * 评论制器
 */
class Comment extends Controller
{
	
    /**
     * 列表
     */
    public function index()
    {
        $model = new CommentModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }
	
	/**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = CommentModel::get($id);
        if ($this->request->isGet()) {
            if($model){
               return $this->renderSuccess('', '', compact('model')); 
            }
            return $this->renderError('获取失败');
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('操作成功', url('order.comment/index'));
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
	
	/**
     * 显示状态编辑
     */
    public function status()
    {
        $data = $this->postData();
        $model = CommentModel::get($data['id']);
		if ($model->status()) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }
	
}
