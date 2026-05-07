<?php
namespace app\store\controller\plat\weixin\wechat\material;

use app\store\controller\Controller;
use app\store\model\Material as MaterialModel;
use think\facade\View;

/**
 * 图片素材
 */
class Image extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new MaterialModel;
        $list = $model->getList(10);
        return View::fetch('index', compact('list'));
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $model = new MaterialModel;
            // 新增记录
            if ($model->add($this->postData('data'))) {
                return $this->renderSuccess('添加成功', url('plat.weixin.wechat.material.image/index'));
            }
            $error = $model->getError() ?: '添加失败';
            return $this->renderError($error);
        }
        return redirect(url('plat.weixin.wechat.material.image/index')); 
    }
    /**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = MaterialModel::get($id);
        if ($this->request->isGet()) {
            if($model){
               return $this->renderSuccess('', '', compact('model')); 
            }
            return $this->renderError('获取失败');
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('plat.weixin.wechat.material.image/index'));
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
        $model = MaterialModel::get($data['id']);
        if (!$model->remove()) {
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return $this->renderSuccess('删除成功');
    }
}