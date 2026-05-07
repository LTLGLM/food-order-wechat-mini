<?php
namespace app\store\controller\market\coupon;

use app\store\controller\Controller;
use app\store\model\CouponLog as CouponLogModel;
use app\store\model\Coupon as CouponModel;
use think\facade\View;

/**
 * 发券记录控制器
 */
class Log extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new CouponLogModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isGet()) {
            $model = new CouponModel;
            $list = $model->getList(false);
            return $this->renderSuccess('', '', compact('list'));
        }
        $model = new CouponLogModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('market.coupon.log/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        $model = CouponLogModel::get($data['id']);
        if ($model->remove()) {
           return $this->renderSuccess('删除成功'); 
        }
		$error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
        
    }
}
