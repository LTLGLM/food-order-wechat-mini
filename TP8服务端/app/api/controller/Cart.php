<?php
namespace app\api\controller;

use app\api\model\Cart as CartModel;
use app\api\model\Setting;

/**
 * 购物车管理
 */
class Cart extends Controller
{
    private $model;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new CartModel($this->user_id);
        //如果是H5平台
        if($this->mp == 'h5'){
            //判断H5下单是否要登录
            $order_login = Setting::getItem('h5')['order_login'];
            if($order_login == 1 and $this->user_id == ''){
                die(json_encode(['code' => -1, 'msg' => '你还没有登录'],JSON_UNESCAPED_UNICODE));
            }
        }
    }
    /**
     * 加入购物车
     */
    public function add()
    {
        if (!$this->model->add($this->request->post())) {
            return $this->renderError($this->model->getError() ?: '操作失败');
        }
        $total_num = $this->model->getTotalNum();
        return $this->renderSuccess(['cart_total_num' => $total_num], '操作成功');
    }
    
    /**
     * 减少购物车商品数量
     */
    public function sub()
    {
        if (!$this->model->sub($this->request->post())) {
            return $this->renderError($this->model->getError() ?: '操作成功');
        }
        $total_num = $this->model->getTotalNum();
        return $this->renderSuccess(['cart_total_num' => $total_num], '操作成功');
    }

    /**
     * 删除购物车中指定商品
     */
    public function delete()
    {
        if (!$this->model->delete($this->request->post())) {
            return $this->renderError($this->model->getError() ?: '操作成功');
        }
        $total_num = $this->model->getTotalNum();
        return $this->renderSuccess(['cart_total_num' => $total_num], '操作成功');
    }
	
	/**
     * 一键清空购物车
     */
    public function clearAll()
    {
		$this->model->clearAll();
        return $this->renderMsg('清除成功');
    }
    
    /**
     * 更改购物车
     */
    public function edit()
    {
        if (!$this->model->edit($this->request->post())) {
            return $this->renderError($this->model->getError() ?: '操作失败');
        }
        $total_num = $this->model->getTotalNum();
        return $this->renderSuccess(['cart_total_num' => $total_num], '操作成功');
    }

}
