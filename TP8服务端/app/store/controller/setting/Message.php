<?php
namespace app\store\controller\setting;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use think\facade\View;

/**
 * 站点消息提醒
 */
class Message extends Controller
{
    /**
     * 短信提醒
     */
    public function sms()
    {
        $list = [];
        if ($addon = \get_addons_info('smsaliyun')) {
            $addon['status'] && array_push($list, ['text' => '阿里云', 'value' => 'aliyun']);
        }
        if ($addon = \get_addons_info('smsqcloud')) {
            $addon['status'] && array_push($list, ['text' => '腾讯云', 'value' => 'qcloud']);
        }
        if ($addon = \get_addons_info('smsqiniu')) {
            $addon['status'] && array_push($list, ['text' => '七牛云', 'value' => 'qiniu']);
        }
        View::assign('list', $list);
        return $this->updateEvent('sms');
    }

    /**
     * 微信提醒
     */
    public function wxtpl()
    {
        return $this->updateEvent('wxtpl');
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
        if ($model->edit($key, $this->postData('data'))) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError('更新失败');
    }
}
