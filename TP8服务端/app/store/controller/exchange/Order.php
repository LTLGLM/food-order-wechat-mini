<?php
namespace app\store\controller\exchange;

use app\store\controller\Controller;
use app\store\model\exchange\Order as OrderModel;
use think\facade\View;

/**
 * 订单管理
 */
class Order extends Controller
{
    
    /**
     * 全部订单列表
     */
    public function all_list(string $search='')
    {
        return $this->getList('全部订单','all',$search);
    }
    /**
     * 待收货订单列表
     */
    public function receipt_list(string $search='')
    {
        return $this->getList('待领取订单','receipt',$search);
    }
    /**
     * 已完成订单列表
     */
    public function complete_list(string $search='')
    {
        return $this->getList('已完成订单','complete',$search);
    }
    /**
     * 被取消订单列表
     */
    public function cancel_list(string $search='')
    {
        return $this->getList('被取消订单','cancel',$search);
    }
    /**
     * 订单列表
     */
    private function getList(string $title, string $dataType, string $search = '')
    {  
        $model = new OrderModel;
        $list = $model->getList($dataType,$search);
        return View::fetch('index', compact('title','list','search'));
    }
    
    /**
     * 商家取消订单
     */
    public function cancel()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->cancel()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 确认完成
     */
    public function receipt()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->receipt()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
}