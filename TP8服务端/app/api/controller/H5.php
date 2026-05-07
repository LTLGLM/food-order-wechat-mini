<?php
namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\model\Setting as SettingModel;

/**
 * 首页控制器
 */
class H5 extends Controller
{
    private $browser = 'other';

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $browser = $_SERVER['HTTP_USER_AGENT']; //获取请求客户端数据 
        if(stristr($browser,'MicroMessenger')){
            $this->browser = 'weixin';
        }elseif(stristr($browser,'AliApp') or stristr($browser,'AlipayClient')){
		    $this->browser = 'alipay';
		}
    }
    
    /**
     * 首页入口
     */
    public function index($scene='applet')
    {
       //判断什么浏览器请求
		if($this->browser == 'weixin'){
		    $app_id = SettingModel::getItem('wechat')['app_id'];//获取公众号APP_ID
		    if(!empty($app_id)){
		        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri=' . urlencode(base_url() . 'api/h5/register') . '&response_type=code&scope=snsapi_base&state='.$scene.'#wechat_redirect';
		    }else{
		        $url = '/h5/#/pages/index/index?scene='.$scene;
		    }
		}elseif($this->browser == 'alipay'){
		    $app_id = SettingModel::getItem('aliweb')['app_id'];//APP_ID
		    if(!empty($app_id)){
		        $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id='.$app_id.'&scope=auth_base&redirect_uri=' . urlencode(base_url() . 'api/h5/register') . '&state='.$scene;
		    }else{
		        $url = '/h5/#/pages/index/index?scene='.$scene;
		    }
		}else{
		    $url = '/h5/#/pages/index/index?scene='.$scene;
		    //return $this->renderError('只能在微信、支付宝中使用');
		}
		return redirect($url);
    }
    
    /**
     * H5自动注册
     */
    public function register()
    {
        $data = $this->request->param();//接收回调数据
        //判断是否为支付宝回调
        isset($data['auth_code']) && $data['code'] = $data['auth_code'];
        $model = new UserModel;
        if($token = $model->h5Register($this->browser,$data['code'])){
            $url = '/h5/#/pages/index/index?token='.$token.'&scene='.$data['state'];
            return redirect($url);
        }
        $error = $model->getError() ?: '登录失败';
        return $this->renderError($error);
    }
    
     /**
     * H5自动登录
     */
    public function login($token='')
    {
        // 筛选条件
        $filter = [];
        if($this->browser == 'weixin'){
            $filter = [
               'wx_open_id' => $token
            ];
        }
        if($this->browser == 'alipay'){
            $filter = [
               'alipay_open_id' => $token
            ];
        }
        $model = new UserModel;
        if($user = $model->where($filter)->find()){
            $user['is_login'] = !empty($user['phone']);//用户电话不为空则为登录状态
            return $this->renderSuccess($user,'登录成功');
        }
        return $this->renderError('用户不存在');
    }
    
}