<?php
namespace app\store\controller\plat\weixin;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use think\facade\View;

/**
 * 微信公众号管理
 */
class Wechat extends Controller
{
	/**
     * 被关注回复设置
     */
    public function subscribe()
    {
        return $this->updateEvent('subscribe');
    }
	
	/**
     * 公众号菜单设置
     */
    public function menus()
    {
        return $this->updateEvent('menus');
    }

    /**
     * 更新设置事件
     */
    private function updateEvent(string $key)
    {
        if (!$this->request->isAjax()) {
            $model = SettingModel::getItem($key);
        	if($key == 'menus'){
				$model = json_encode($model);
        	}
            return View::fetch($key, compact('model'));
        }
        $model = new SettingModel;
        if ($model->edit($key,$this->postData('data'))){
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
		return $this->renderError($error);
    }
}
