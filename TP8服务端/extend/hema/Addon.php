<?php
namespace hema;

use think\facade\Config;
use think\facade\Cache;
use hema\Http;

/**
 * 插件接口类
 **/
class Addon
{
    private const REMOTE_DISABLED_MESSAGE = '远程插件市场和在线升级已禁用';
    private $api_url;
    private $token = '';
    private $error;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->api_url = Config::get('app.hemaphp.api_url');//获取接口域名
        $user = Cache::get('hemaphp',[]);//获取登录信息
        isset($user['token']) && $this->token = $user['token'];
    }

    /**
     * 获取插件列表
     */
    public function getAddonList($page = 1)
    {
        if (!$this->remoteEnabled()) {
            return [
                'list' => [
                    'data' => [],
                    'total' => 0,
                ],
                'page' => null,
            ];
        }
        $url = $this->api_url . '/api/addon/lists';
        $queryarr = [
            'page' => $page,
            'system' => Config::get('app.hemaphp.system'),
        ];
        $result = json_decode(Http::post($url, $queryarr),true);
        $addons = \get_addons_list();
        for($n=0;$n<sizeof($result['data']['list']['data']);$n++){
            if(isset($addons[$result['data']['list']['data'][$n]['name']])){
                $result['data']['list']['data'][$n]['addon'] = $addons[$result['data']['list']['data'][$n]['name']];
            }
        }
        return $result['data'];
    }

    /**
     * 获取插件详情
     */
    public function getAddonDetail($name)
    {
        if (!$this->remoteEnabled()) {
            return false;
        }
        $url = $this->api_url . '/api/addon/detail';
        $queryarr['name'] = $name;
        return $this->result(json_decode(Http::post($url, $queryarr),true)); 
    }
    
    /**
     * 验证是否登录
     */
    public function checkLogin()
    {
        if (!$this->remoteEnabled()) {
            return false;
        }
        if($this->token){
            $url = $this->api_url . '/api/user/checkAddonLogin';
            $queryarr['token'] = $this->token;
            return $this->result(json_decode(Http::post($url, $queryarr),true)); 
        }
        $this->error = '未登录';
        return false;
    }
    
    /**
     * 推出登录
     */
    public function logout()
    {
        if (!$this->remoteEnabled()) {
            return false;
        }
        $url = $this->api_url . '/api/user/addonLogout';
        $queryarr['token'] = $this->token;
        $result = json_decode(Http::post($url, $queryarr),true); 
        if($result['code']!=0){
			$this->error = $result['msg'];
			return false;
		}
        Cache::delete('hemaphp');
		return true;
        
    }
    
    /**
     * 用户登录
     */
    public function login($queryarr)
    {
        if (!$this->remoteEnabled()) {
            return false;
        }
        $url = $this->api_url . '/api/user/addonLogin';
        $result = json_decode(Http::post($url, $queryarr),true);
        if($result['code']!=0){
			$this->error = $result['msg'];
			return false;
		}
		Cache::set('hemaphp',$result['data']);
		return $result['data'];
    }
    
    /**
     * 获取框架新版本
     */
    public function news()
    {
        if (!$this->remoteEnabled()) {
            return false;
        }
        $url = $this->api_url . '/api/hema/news';
        $queryarr = [
            'domain' => domain(),
            'system' => Config::get('app.hemaphp.system'),
            'hmversion' => config('app.hemaphp.version'),
        ];
        if($result = json_decode(Http::post($url, $queryarr),true)){
            if(isset($result['code']) AND $result['code'] !=0 ){
    			$this->error = $result['msg'];
    			return false;
    		}
    		if(isset($result['data']['version'])){
    			return hema_json($result['data']);
    		}
        }
		return false;
    }
    
    /**
     * 请求数据验证
     **/
    private function result($result)
    {
        if($result['code']!=0){
			$this->error = $result['msg'];
			return false;
		}
		return $result['data'];
    }
    
    public function getError()
    {
        return $this->error;
    }

    private function remoteEnabled()
    {
        if (!Config::get('app.hemaphp.remote_enabled')) {
            $this->error = self::REMOTE_DISABLED_MESSAGE;
            return false;
        }
        return true;
    }

}
