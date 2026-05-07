<?php
namespace app\api\controller;

use app\api\model\User as UserModel;
use think\response\Json;

/**
 * API控制器基类
 */
class Controller extends \app\BaseController
{
    const JSON_SUCCESS_STATUS = 1;
    const JSON_ERROR_STATUS = 0;
	protected $user_id;    //用户ID
    protected $mp;    //运行平台
    
    /**
     * 基类初始化
     */
    public function initialize()
    {
        $this->mp = $this->request->param('mp');
        $this->user_id = $this->request->param('user_id');
    }

    /**
     * 获取当前用户信息
     */
    protected function getUserDetail()
    {
        return UserModel::getDetail($this->user_id);
    }
    /**
     * 返回封装后的 API 数据到客户端
     */
    protected function renderJson($code = self::JSON_SUCCESS_STATUS, $msg = '', $data = [])
    {
        return json(compact('code', 'msg', 'data'));
    }
    /**
     * 返回操作成功json - 有返回值
     */
    protected function renderSuccess($data = [], $msg = 'success')
    {
        return $this->renderJson(self::JSON_SUCCESS_STATUS, $msg, $data);
    }
	/**
     * 返回操作成功json - 无返回值
     */
    protected function renderMsg($msg = 'success')
    {
        return $this->renderJson(self::JSON_SUCCESS_STATUS, $msg);
    }
    /**
     * 返回操作失败json
     */
    protected function renderError($msg = 'error', $code = self::JSON_ERROR_STATUS,$data = [])
    {
        return $this->renderJson($code, $msg, $data);
    }
    /**
     * 获取post数据 (数组)
     */
    protected function postData($key = null)
    {
        return $this->request->post(is_null($key) ? '' : $key . '/a');
    }
    /**
     * 获取post数据 (数组)
     * @param $key
     * @return mixed
     */
    protected function postForm($key = 'form')
    {
        return $this->postData($key);
    } 
}