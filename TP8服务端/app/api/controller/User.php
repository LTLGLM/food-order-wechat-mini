<?php
namespace app\api\controller;

use app\api\model\User as UserModel;
use app\api\model\Setting;
use hema\sms\Driver as Sms;
use think\facade\Cache;

/**
 * 用户管理
 */
class User extends Controller
{
    /**
     * 自动登录/注册
     */
    public function autoLogin()
    {
        $model = new UserModel;
        if($detail = $model->autoLogin($this->request->post())){
            return $this->renderSuccess($detail,'登录成功');
        }
        $error = $model->getError() ?: '登录失败';
        return $this->renderError($error);
    }
    
    /**
     * 获取用户手机号
     */
    public function getPhoneNumber()
    {
        $userInfo = $this->getUserDetail();
        $model = new UserModel;
        if ($detail = $model->getPhoneNumber($this->request->post(),$userInfo)) {
            return $this->renderSuccess($detail,'登录成功');
        }
        $error = $model->getError() ?: '手机号获取失败';
        return $this->renderError($error);
    }
    /**
     * 获取当前用户信息
     */
    public function detail()
    {
        // 当前用户信息
        $model = $this->getUserDetail();
        if (!$this->request->isPost()) {
            return $this->renderSuccess($model);
        }
        if ($model->edit($this->request->post())) {
            return $this->renderMsg('更新成功');
        }
        $error = $model->getError() ?: '更新失败';
		return $this->renderError($error);
    }
    
    /**
	 * 发动短信验证码
	 */
	public function sendsms()
    {
        $data = $this->request->post();
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
        if($sms->sendSms($data['phone'], ['code' => $code])){
            Cache::set($data['phone'] . '_' . $code,'sms_captcha',300);
            return $this->renderMsg('发送成功'); 
        }
        $error = $sms->getError() ?: '短信验证码发送失败';
        return $this->renderError($error);

	}
	
	 /**
     * 手机验证码登录
     */
    public function checkPhoneCaptcha()
    {
        $model = new UserModel;
        if($detail = $model->checkPhoneCaptcha($this->request->post())){
            return $this->renderSuccess($detail,'登录成功');
        }
        $error = $model->getError() ?: '登录失败';
        return $this->renderError($error);
    }
}
