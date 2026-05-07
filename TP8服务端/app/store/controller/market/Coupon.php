<?php
namespace app\store\controller\market;

use app\store\controller\Controller;
use app\store\model\Coupon as CouponModel;
use think\facade\View;

/**
 * 优惠券券控制器
 */
class Coupon extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new CouponModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            return View::fetch('add');
        }
        $model = new CouponModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('market.coupon/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑
     */
    public function edit(int $id)
    {
        //详情
        $model = CouponModel::get($id);
        if (!$this->request->isAjax()) {
            return View::fetch('edit', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('market.coupon/index'));
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        $model = CouponModel::get($data['id']);
        if ($model->remove()) {
           return $this->renderSuccess('删除成功'); 
        }
		$error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
        
    }
}
