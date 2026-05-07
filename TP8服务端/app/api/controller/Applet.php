<?php
namespace app\api\controller;

use app\api\model\Page as PageModel;
use app\api\model\Setting;

/**
 * 小程序
 */
class Applet extends Controller
{
    /**
     * 小程序基础信息
     */
    public function base()
    {
        $page = PageModel::getHome()['page_data']['array'];
        $applet['navbar'] = $page['page'];
        $applet['tabbar'] = $page['tabbar'];

        $system = config('app.hemaphp.system');
        $applet['system'] = $system;
        $config = \get_addons_config($system);
        $applet['copyright'] = $config['copyright'];
        $applet['version'] = \get_addons_info($system)['version'];
        $applet['shop'] = Setting::getItem('shop');
        $applet['theme'] = 'default';
        $applet['is_arraign'] = false;
        return $this->renderSuccess($applet);
    }
}
