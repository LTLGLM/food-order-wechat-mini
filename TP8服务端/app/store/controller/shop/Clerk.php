<?php
namespace app\store\controller\shop;

use app\store\controller\Controller;
use app\store\model\ShopClerk as ShopClerkModel;
use think\facade\View;

/**
 * 店员管理控制器
 */
class Clerk extends Controller
{
    /**
     * 列表
     */
    public function index(string $search='')
    {
        $model = new ShopClerkModel;
        $list = $model->getList($search);
        $time = time();
        return View::fetch('index', compact('list','search','time'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $model = new ShopClerkModel;
            if ($model->add($this->postData('data'))) {
                return $this->renderSuccess('添加成功', url('shop.clerk/index'));
            }
            $error = $model->getError() ?: '添加失败';
            return $this->renderError($error);
        }
        return redirect(url('shop.clerk/index'));
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        $model = ShopClerkModel::get($data['id']);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

    /**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = ShopClerkModel::get($id);
        if ($this->request->isGet()) {
            if($model){
               return $this->renderSuccess('', '', compact('model')); 
            }
            return $this->renderError('获取失败');
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('操作成功', url('shop.clerk/index'));
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 微信绑定
     */
    public function wechatBind()
    {
        $data = $this->postData();
        $model = ShopClerkModel::get($data['id']);
        $data = $this->postData('data');
        $filter = [
            'wx_open_id' => $data['openid']    
        ];
        if($user = (new ShopClerkModel)->withoutGlobalScope()->where($filter)->find()){
            if($user['shop_clerk_id'] != $model['shop_clerk_id']){
                return $this->renderError('该微信已绑定在其它账号');
            }else{
                return $this->renderError('不要重复绑定该微信');
            }
        }
        if ($model->edit($filter)) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
	}

}
