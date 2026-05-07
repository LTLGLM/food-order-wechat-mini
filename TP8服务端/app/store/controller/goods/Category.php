<?php
namespace app\store\controller\goods;

use app\store\controller\Controller;
use app\store\model\Category as CategoryModel;
use think\facade\View;

/**
 * 商品分类
 */
class Category extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new CategoryModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isGet()) {
            return $this->renderSuccess();
        }
        $model = new CategoryModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('goods.category/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = CategoryModel::get($id);
        if ($this->request->isGet()) {
            if($model){
               return $this->renderSuccess('', '', compact('model')); 
            }
            return $this->renderError('获取失败');
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('操作成功', url('goods.category/index'));
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        $model = CategoryModel::get($data['id']);
        if ($model->remove()) {
            return $this->renderSuccess('删除成功');
        }
		$error = $model->getError() ?: '删除失败';
		return $this->renderError($error);
    }

}
