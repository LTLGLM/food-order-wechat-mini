<?php
namespace app\store\controller\exchange;

use app\store\controller\Controller;
use app\store\model\exchange\Goods as GoodsModel;
use think\facade\View;

/**
 * 商品管理控制器
 */
class Goods extends Controller
{

    /**
     * 商品列表
     */
    public function index(int $goods_type = 0,string $search = '')
    {
        $model = new GoodsModel;
        $list = $model->getList($goods_type,$search);
        return View::fetch('index', compact('list','search'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            return View::fetch('add');
        }
        $model = new GoodsModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('exchange.goods/index'));
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
            return View::fetch('edit', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('exchange.goods/index'));
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
