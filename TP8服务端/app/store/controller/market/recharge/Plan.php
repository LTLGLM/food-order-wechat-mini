<?php
namespace app\store\controller\market\recharge;

use app\store\controller\Controller;
use app\store\model\RechargePlan as RechargePlanModel;
use think\facade\View;

/**
 * 用户充值套餐控制器
 */
class Plan extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new RechargePlanModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        $model = new RechargePlanModel;
        if (!$this->request->isAjax()) {
            return View::fetch('add');
        }
        // 新增记录
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('market.recharge.plan/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 更新
     */
    public function edit(int $id)
    {
        // 详情
        $model = RechargePlanModel::get($id);
        if (!$this->request->isAjax()) {
            return View::fetch('edit', compact('model'));
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('market.recharge.plan/index'));
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
        // 详情
        $model = RechargePlanModel::get($data['id']);
        if (!$model->remove()) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }

}
