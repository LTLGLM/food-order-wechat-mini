<?php
namespace app\store\controller\market;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use app\store\model\SignLog as SignLogModel;
use think\facade\View;

/**
 * 用户签到
 */
class Sign extends Controller
{
    /**
     * 用户签到记录
     */
    public function logs()
    {
        $model = new SignLogModel;
        $list = $model->getList();
        return View::fetch('logs', compact('list'));
    }
    
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post_data = $this->postData('data');
            $data = SettingModel::getItem('sign');
            if(is_array($data['plan'])){
                array_push($data['plan'],$post_data);
            }else{
               $data['plan'][0] = $post_data;
            }
            $model = new SettingModel;
            if ($model->edit('sign',$data)) {
                return $this->renderSuccess('添加成功', url('market.setting/sign'));
            }
            $error = $model->getError() ?: '添加失败';
            return $this->renderError($error);
        }
        return redirect(url('market.setting/sign'));
    }
    
    /**
     * 编辑
     */
    public function edit(int $id)
    {
        if ($this->request->isPost()) {
            $post_data = $this->postData('data');
            $data = SettingModel::getItem('sign');
            $data['plan'][$id] = $post_data;
            $model = new SettingModel;
            if ($model->edit('sign',$data)) {
                return $this->renderSuccess('编辑成功', url('market.setting/sign'));
            }
            $error = $model->getError() ?: '编辑失败';
            return $this->renderError($error);
        }
        return redirect(url('market.setting/sign'));
    }
    
    /**
     * 删除
     */
    public function delete()
    {
        $data = $this->postData();
        if ($this->request->isPost()) {
            $data = SettingModel::getItem('sign');
            array_splice($data['plan'],$data['id'],1);
            $model = new SettingModel;
            if ($model->edit('sign',$data)) {
                return $this->renderSuccess('删除成功', url('market.setting/sign'));
            }
            $error = $model->getError() ?: '删除失败';
            return $this->renderError($error);
        }
        return redirect(url('market.setting/sign'));
    }
    
}