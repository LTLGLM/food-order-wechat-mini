<?php
namespace app\store\controller\order;

use app\store\controller\Controller;
use app\store\model\Pact as PactModel;
use think\facade\View;

/**
 * 预约控制器
 */
class Pact extends Controller
{
	
    /**
     * 全部订单
     */
    public function index(string $search='')
    {
        return $this->getList('全部订单',0,$search);
    }
    /**
     * 待确认订单
     */
    public function pact10_list(string $search='')
    {
        return $this->getList('待确认订单',10,$search);
    }
    /**
     * 已过期订单
     */
    public function pact20_list(string $search='')
    {
        return $this->getList('已过期订单',20,$search);
    }
    /**
     * 已完成订单
     */
    public function pact30_list(string $search='')
    {
        return $this->getList('已完成订单',30,$search);
    }
    /**
     * 已取消订单
     */
    public function pact40_list(string $search='')
    {
        return $this->getList('已取消订单',40,$search);
    }
    /**
     * 已确认订单
     */
    public function pact50_list(string $search='')
    {
        return $this->getList('已确认订单',50,$search);
    }
    
    /**
     * 订单列表
     */
    private function getList(string $title, int $status, string $search = '')
    {  
        $model = new PactModel;
        $list = $model->getList($status,$search);
        return View::fetch('index', compact('title','list','search'));
    }

	/**
     * 状态编辑
     */
    public function status(int $id)
    {
        $data = $this->postData('data');
        $model = PactModel::get($id,['user']);
		if ($model->status($data['status'])) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
    }
	
}
