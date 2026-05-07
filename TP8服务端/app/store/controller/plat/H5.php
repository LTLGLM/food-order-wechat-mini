<?php
namespace app\store\controller\plat;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use think\facade\View;

/**
 * H5平台控制器
 */
class H5 extends Controller
{
    /**
	 * H5平台设置
	 */
	public function h5()
	{
	    return $this->updateEvent('h5');
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
        if ($model->edit($key,$this->postData('data'))) {
            return $this->renderSuccess('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
        return $this->renderError($error);
    }
}
