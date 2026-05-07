<?php
namespace app\store\controller;

use app\store\model\ShopClerk as ShopClerkModel;
use app\store\model\Setting;
use think\facade\View;
use think\facade\Session;
use think\facade\Cache;
use think\captcha\facade\Captcha;
use hema\sms\Driver as Sms;
use hema\wechat\Wechat;

/**
 * 商家后台认证
 */
class Passport extends Controller
{
    /**
     * 生成验证码
     **/
    public function captcha()
    {
        return Captcha::create();
    }

    /**
     * 商家用户登录
     */
    public function login()
    {
        if (!$this->request->isAjax()) {
            // 验证登录状态
            if (isset($this->user) AND (int)$this->user['is_login'] === 1) {
                return redirect(url('index/index'));
            }
            View::layout(false);
            return View::fetch();
        }
        $model = new ShopClerkModel;
        if ($model->login($this->postData('data'))) {
            return $this->renderSuccess('登录成功', url('index/index'));
        }
        $error = $model->getError() ?: '登录失败';
        return $this->renderError($error,'',[],-1);
    }

    /**
     * 更新密码
     */
    public function renew()
    {
        $model = (new ShopClerkModel)->where('phone',$this->user['user']['phone'])->find();
        if ($model->renew($this->postData('data'))) {
            // 清空登录状态
            Session::delete('hema_store');
            return $this->renderSuccess('请重新登录',url('passport/login'));
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        // 清空登录状态
        Session::delete('hema_store');
        return $this->renderSuccess('成功退出',url('passport/login'));
    }
    
    /**
	 * 发送短信验证码
	 */
	public function sendsms(string $phone)
    {
        $values = Setting::getItem('sms');
        if($values['gateway'] == ''){
            return $this->renderError('未配置短信平台');
        }
        if($values['scene']['captcha'] == 0){
            return $this->renderError('未开启短信验证码');
        }

        if($error = get_addons_status('sms'.$values['gateway'])){
            return $this->renderError($error);
        }
        $sms = new Sms($values['gateway']);
        $code = get_captcha();
        if($sms->sendSms($phone, ['code' => $code])){
            Cache::set($phone . '_' . $code,'sms_captcha',300);
            return $this->renderSuccess('发送成功'); 
        }
        $error = $sms->getError() ?: '短信验证码发送失败';
        return $this->renderError($error);
	}
	
	/**
     * 获取关注二维码
     */
    public function qrcodeCreate()
    {
        $wx = new Wechat;
        if($ticket = $wx->qrcodeCreate()){
            return $this->renderSuccess('操作成功','',compact('ticket'));
        }
        $error = $wx->getError() ?: '二维码获取失败';
        return $this->renderError($error);
    }
    
    /**
	 * 检测是否扫码（关注公众号登录）
	 */
	public function checklogin(string $ticket='')
    {
        if($scan = Cache::get($ticket)){
            Cache::delete($ticket);
            return $this->renderSuccess('扫码成功','',$scan);
        }
        return $this->renderError('等待扫码');
	}
}
