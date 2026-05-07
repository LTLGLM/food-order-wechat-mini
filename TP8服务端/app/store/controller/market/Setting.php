<?php
namespace app\store\controller\market;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use think\facade\View;

/**
 * 设置控制器
 */
class Setting extends Controller
{
    /**
     * 会员卡设置
     */
    public function vip()
    {
        return $this->updateEvent('vip');
    }
    
    /**
     * 充值设置
     */
    public function recharge()
    {
        return $this->updateEvent('recharge');
    }
    
    /**
     * 签到设置
     */
    public function sign()
    {
        return $this->updateEvent('sign');
    }
    
    /**
     * 更新设置事件
     */
    private function updateEvent(string $key)
    {
        if (!$this->request->isAjax()) {
			$model = SettingModel::getItem($key);
            return View::fetch($key, compact('model'));
        }
        $model = new SettingModel;
        if($model->edit($key, $this->postData('data'))) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }
}