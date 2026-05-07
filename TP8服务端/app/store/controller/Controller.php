<?php
namespace app\store\controller;

use app\store\model\Setting;
use think\facade\View;
use think\facade\Session;
use think\facade\Config;
use hema\Addon;

/**
 * 管理后台控制器基类
 */
class Controller extends \app\BaseController
{
    protected $user;    // 用户登录信息
    protected $controller = ''; // 当前控制器名称
    protected $action = '';     // 当前方法名称
    protected $routeUri = '';   // 当前路由uri
    protected $group = '';  // 当前路由：分组名称

    // 登录验证白名单
    protected $allowAllAction = [
        'passport/login',
        'passport/captcha',
    ];

    /**
     * 后台初始化
     */
    public function initialize()
    {
        $this->getUserInfo();// 获取登录信息
        $this->getRouteInfo();// 当前路由信息
        $this->checkLogin();// 验证登录状态
        $this->layout();
    }

    /**
     * 获取登录信息
     */
    private function getUserInfo()
    {
        $this->user = Session::get('hema_store');
    }

    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteInfo()
    {
        // 控制器名称
        $this->controller = uncamelize($this->request->controller());
        // 方法名称
        $this->action = $this->request->action();
        // 控制器分组 (用于定义所属模块)
        $group = strstr($this->controller, '.', true);
        $this->group = $group !== false ? $group : $this->controller;
        // 当前uri
        $this->routeUri = "{$this->controller}/$this->action";
    }
    
    /**
     * 验证登录状态
     */
    private function checkLogin()
    {
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return true;
        }
        // 验证登录状态
        if (empty($this->user) || (int)$this->user['is_login'] !== 1) {
            return redirect((string)url('passport/login'))->send();
        }
        return true;
    }

    /**
     * 全局layout模板输出
     */
    private function layout()
    {  
        $addon = new Addon;
        $is_new = $addon->news();
        // 输出到view
        View::assign([
            'base_url' => base_url(),            // 当前站点域名
            'store_url' => '/store/',       // 后台模块url
            'base' => Setting::getItem('base'),    //站点设置
            'group' => $this->group,
            'menus' => $this->menus(),          // 后台菜单
            'user' => $this->user,            // 登录信息
            'version' => Config::get('app.hemaphp.version'),
            'is_new' => $is_new,
        ]);
    }

    /**
     * 后台菜单配置
     */
    private function menus()
    {
        foreach ($data = Config('menus') as $group => $first) {
            $data[$group]['active'] = $group === $this->group;
            //$data[0]['active']
            // 遍历：二级菜单
            if (isset($first['submenu'])) {
                foreach ($first['submenu'] as $secondKey => $second) {
                    // 二级菜单所有uri
                    $secondUris = [];
                    if (isset($second['submenu'])) {
                        // 遍历：三级菜单
                        foreach ($second['submenu'] as $thirdKey => $third) {
                            $thirdUris = [];
                            if (isset($third['uris'])) {
                                $secondUris = array_merge($secondUris, $third['uris']);
                                $thirdUris = array_merge($thirdUris, $third['uris']);
                            } else {
                                $secondUris[] = $third['index'];
                                $thirdUris[] = $third['index'];
                            }
                            $data[$group]['submenu'][$secondKey]['submenu'][$thirdKey]['active'] = in_array($this->routeUri, $thirdUris);
                            $data[$group]['submenu'][$secondKey]['active'] = in_array($this->routeUri, $secondUris);
                        }
                    } else {
                        if (isset($second['uris']))
                            $secondUris = array_merge($secondUris, $second['uris']);
                        else
                            $secondUris[] = $second['index'];
                    }
                    // 二级菜单：active
                    !isset($data[$group]['submenu'][$secondKey]['active'])
                    && $data[$group]['submenu'][$secondKey]['active'] = in_array($this->routeUri, $secondUris); 
                }
            }
        }
        return $data;
    }

    /**
     * 返回封装后的 API 数据到客户端
     */
    protected function renderJson(int $code = 1, string $msg = '', string $url = '', array $data = [])
    {
        return json(compact('code', 'msg', 'url', 'data'));
    }

    /**
     * 返回操作成功json
     */
    protected function renderSuccess(string $msg = 'success', string $url = '', array $data = [], int $code = 1)
    {
        return $this->renderJson($code, $msg, $url, $data);
    }

    /**
     * 返回操作失败json
     */
    protected function renderError(string $msg = 'error', string $url = '', array $data = [], int $code = 0)
    {
        return $this->renderJson($code, $msg, $url, $data);
    }

    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postData(string $key = '')
    {
        return $this->request->post(empty($key) ? '' : "{$key}/a");
    }

    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postForm(string $key = 'form')
    {
        return $this->postData($key);
    }

    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function getData(string $key = '')
    {
        return $this->request->get(empty($key) ? '' : $key);
    }
}
