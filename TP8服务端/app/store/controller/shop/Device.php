<?php
namespace app\store\controller\shop;

use app\store\controller\Controller;
use app\store\model\Device as DeviceModel;
use app\store\model\Category as CategoryModel;
use think\facade\View;
use hema\device\Driver;

/**
 * 云设备控制器
 */
class Device extends Controller
{
    /**
     * 列表
     */
    public function index()
    {
        $model = new DeviceModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isAjax()) {
            $model = new CategoryModel;
            $category = $model->getList(false);
            $dr = new Driver;
            $company = $dr->company();
            return View::fetch('add', compact('category','company'));
        }
        $model = new DeviceModel;
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('shop.device/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑
     */
    public function edit(int $id)
    {
        $model = DeviceModel::get($id);
        if (!$this->request->isAjax()) {
            $category = $this->getChecked($model->toArray());
            return View::fetch('edit', compact('model', 'category'));
        }
        // 更新记录
        if ($model->edit($this->postData('data'))) {
            return $this->renderSuccess('更新成功', url('shop.device/index'));
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
        $model = DeviceModel::get($data['id']);
        if($model->remove()) {
            return $this->renderSuccess('删除成功');
        }
        $error = $model->getError() ?: '删除失败';
        return $this->renderError($error);
    }

    /**
     * 开启/关闭
     */
    public function status()
    {
        $data = $this->postData();
        $model = DeviceModel::get($data['id']);
        // 更新记录
        if ($model->status()) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }
    
    /**
     * 编辑打印类目
     **/
    private function getChecked(array $data)
    {
        $model = new CategoryModel;
        $category = $model->getList(false);
        for($n=0;$n<sizeof($category);$n++){
            $category[$n]['checked'] = false;
            if(isset($data['values']['category'])){
                foreach ($data['values']['category'] as $item){
                    if($category[$n]['category_id'] == $item){
                       $category[$n]['checked'] = true; 
                    }
                }
            }
        }
        return $category;
    }
}
