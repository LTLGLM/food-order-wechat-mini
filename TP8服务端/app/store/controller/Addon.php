<?php
namespace app\store\controller;

use hema\Addon as AddonModel;
use think\addons\Service;
use think\facade\Cache;
use think\facade\Config;
use think\facade\View;

/**
 * 插件控制器
 */
class Addon extends Controller
{
    private const REMOTE_DISABLED_MESSAGE = '远程插件市场、在线安装和在线升级已禁用';

    /**
     * 我的插件
     */
    public function my()
    {
        $addonuser = Cache::get('hemaphp', []);
        $addonList = get_addons_list();
        $list = [];
        $model = $this->remoteEnabled() ? new AddonModel : null;

        foreach ($addonList as $key => $item) {
            if ($model && ($addon = $model->getAddonDetail($key))) {
                $item['addon'] = $addon;
            }
            $list[] = $item;
        }

        return View::fetch('my', compact('list', 'addonuser'));
    }

    /**
     * 插件市场
     */
    public function index(int $page = 1)
    {
        if (!$this->remoteEnabled()) {
            return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
        }

        $addonuser = Cache::get('hemaphp', []);
        $model = new AddonModel;
        $result = $model->getAddonList($page);
        $list = $result['list'];
        $page = $result['page'];
        if (!is_null($page)) {
            $page = str_replace("/api/addon/lists", "/store/addon/index", $result['page']);
        }
        return View::fetch('index', compact('list', 'page', 'addonuser'));
    }

    public function checkLogin()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    public function login()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    public function logout()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    /**
     * 在线安装
     */
    public function install()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    /**
     * 离线安装
     */
    public function local()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    /**
     * 在线升级
     */
    public function upgrade()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    /**
     * 卸载
     */
    public function uninstall()
    {
        $data = $this->request->post();
        $addon = get_addons_info($data['name']);
        if ($addon['status'] == 1) {
            return $this->renderError('禁用插件后才能操作');
        }
        $result = Service::uninstall($data['name']);
        if (is_array($result)) {
            $code = 0;
            $payload = [];
            if (isset($result['data'])) {
                $payload = $result['data'];
            }
            if (isset($result['code'])) {
                $result['code'] < 0 && $code = $result['code'];
            }
            return $this->renderError($result['msg'], '', $payload, $code);
        }
        return $this->renderSuccess('卸载成功');
    }

    /**
     * 启用/禁用
     */
    public function status()
    {
        $data = $this->request->post();
        $action = $data['status'] == 1 ? 'disable' : 'enable';
        $result = Service::$action($data['name']);
        if (is_array($result)) {
            $code = 0;
            $payload = [];
            if (isset($result['data'])) {
                $payload = $result['data'];
            }
            if (isset($result['code'])) {
                $result['code'] < 0 && $code = $result['code'];
            }
            return $this->renderError($result['msg'], '', $payload, $code);
        }
        return $this->renderSuccess('操作成功');
    }

    /**
     * 配置
     */
    public function config(string $name)
    {
        if ($this->request->isGet()) {
            if ($config = get_addons_config($name, true)) {
                return $this->renderSuccess('', '', $config);
            }
            return $this->renderError('获取失败');
        }
        $data = $this->request->post('data');
        if (set_addons_config($name, $data)) {
            return $this->renderSuccess('配置成功');
        }
        return $this->renderError('配置失败');
    }

    /**
     * 购买插件
     */
    public function pay(int $addon_id)
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    /**
     * 框架升级
     */
    public function hemaphpUpgrade()
    {
        return $this->renderError(self::REMOTE_DISABLED_MESSAGE);
    }

    private function remoteEnabled(): bool
    {
        return (bool) Config::get('app.hemaphp.remote_enabled');
    }
}
