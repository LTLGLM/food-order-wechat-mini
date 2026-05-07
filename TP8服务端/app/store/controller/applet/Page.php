<?php
namespace app\store\controller\applet;

use app\store\controller\Controller;
use app\store\model\Page as PageModel;
use think\facade\View;

/**
 * 小程序页面管理
 */
class Page extends Controller
{
    
    /**
     * 获取页面列表
     */
    public function index()
    {
        $model = new PageModel;
        $list = $model->getList();
        return View::fetch('index', compact('list'));
    }

    /**
     * 添加
     */
    public function add()
    {
        $model = new PageModel;
        if (!$this->request->isAjax()) {
			$temp = PageModel::temp()['json'];
            $jsonData = $model->insertDefault();
			$opts['category'] = '';//Category::getCacheTree();//获取商品分类
			$opts['sharingcategory'] ='';
			$opts['articlecategory'] = ''; //获取图文分类
			$opts = json_encode($opts); //转换成json格式
            return View::fetch('add', compact('temp','jsonData','opts'));
        }
        if ($model->add($this->postData('data'))) {
            return $this->renderSuccess('添加成功', url('applet.page/index'));
        }
        $error = $model->getError() ?: '添加失败';
        return $this->renderError($error);
    }

    /**
     * 编辑
     */
    public function edit(int $id)
    {
        // 详情
        $model = PageModel::get($id);
        if (!$this->request->isAjax()) {
			$temp = PageModel::temp()['json'];
			$opts['category'] = '';//Category::getCacheTree();//获取商品分类
			$opts['sharingcategory'] ='';
            $opts['articlecategory'] = ''; //获取图文分类
			$opts = json_encode($opts); //转换成json格式
            $jsonData = $model['page_data']['json'];
            return View::fetch('edit', compact('temp','jsonData','opts'));
        }
        $data = $this->postData('data');
        if (!$model->edit($data)) {
            return $this->renderError('更新失败');
        }
        return $this->renderSuccess('更新成功');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        $model = PageModel::get($data['id']);
        if (!$model->remove()) {
            return $this->renderError('删除失败');
        }
        return $this->renderSuccess('删除成功');
    }
	
	/**
     * 设置默认首页
     */
	public function status()
    {
        $data = $this->postData();
        $model = PageModel::get($data['id']);
        if ($model->status()) {
            return $this->renderSuccess('设置成功',url('applet.page/index'));
        }
		return $this->renderError('设置失败');
    }
}
