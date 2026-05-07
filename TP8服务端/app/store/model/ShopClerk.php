<?php
namespace app\store\model;

use app\common\model\ShopClerk as ShopClerkModel;
use think\facade\Session;

/**
 * 店员模型
 */
class ShopClerk extends ShopClerkModel
{
    /**
     * 登录
     */
    public function login(array $data)
    {
        if(!captcha_check($data['captcha'])){
            $this->error = '验证码错误';
            return false;
        }
        $filter = [
            'phone' => $data['phone'],
            'status' => 20, //店长
        ];
        // 验证用户名密码是否正确
        if (!$user = $this->withoutGlobalScope()->where($filter)->find()){
            $this->error = '账号不存在';
            return false;
        }
        if($user['password'] != hema_hash($data['password'])){
            $this->error = '密码错误';
            return false;
        }
        /*
        $user->login_ip = \request()->ip();//用户终端IP
        $user->login_time = time();
        $user->save();
        */
        unset($user['password']);
        $user = $user->toArray();
        // 保存登录状态
        Session::set('hema_store', [
            'user' => $user,
            'is_login' => true,
        ]);
        return true;
    }
}
