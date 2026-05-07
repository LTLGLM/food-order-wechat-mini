<?php
namespace app\store\controller;

use app\store\model\Category as CategoryModel;
use app\store\model\Goods as GoodsModel;
use think\facade\View;

/**
 * 商品管理控制器
 */
class Goods extends Controller
{

    /**
     * 商品列表
     */
    public function index(string $search = '')
    {
        $model = new GoodsModel;
        $list = $model->getList($search);
        return View::fetch('index', compact('list','search'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            $model = new CategoryModel;
            $category = $model->getList(false);
            return View::fetch('add', compact('category'));
        }
        $model = new GoodsModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('goods/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }
    
    /**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = GoodsModel::detail($id);
        if (!$this->request->isAjax()) {
            $cls = new CategoryModel;
            $category = $cls->getList(false);
            // 多规格信息
            $specData = 'null';
            if ($model['spec_type'] == 20)
                $specData = json_encode($model->getManySpecData($model['spec_rel'], $model['spec']));
            return View::fetch('edit', compact('model', 'category', 'specData'));
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('goods/index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }

    /**
     * 删除商品
     */
    public function delete()
    {
        $data = $this->postData();
        $model = GoodsModel::get($data['id']);
        if ($model->remove()) {
           return $this->renderSuccess('删除成功'); 
        }
		$error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
        
    }
	
	
	/**
     * 上架/下架
     */
	public function status()
	{
	    $data = $this->postData();
		$model = GoodsModel::get($data['id']);
		if($model->status()){
			return $this->renderSuccess('操作成功');
		}
		$error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
	}
}
