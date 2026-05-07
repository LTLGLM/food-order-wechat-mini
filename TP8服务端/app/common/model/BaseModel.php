<?php
namespace app\common\model;

use think\Model;
use think\facade\Session;
use think\facade\Cache;
use think\facade\Request;

/**
 * 模型基类
 */
class BaseModel extends Model
{
    // 定义表名
    protected $name;

    // 模型别名
    protected $alias = '';

    // 错误信息
    protected $error = '';
    
    /**
     * 模型基类初始化
     */
    public static function init()
    {
        parent::init();
        self::limit();
    }

    /**
     * 权限验证
     */
    private static function limit()
    {
        $app_name = app_name();
        if($app_name == 'store'){
            //设置测试用户权限
            $session = Session::get('hema_store');
            if(isset($session['user']) AND $session['user']['phone'] == 'test'){
                if(Request::isPost() && !in_array(Request::action(), ['logout'])){
                   die(json_encode(['code' => 0, 'msg' => '体验用户，无权操作！'],JSON_UNESCAPED_UNICODE));
                }
            }
        }
		if ($app_name == 'api') {
            $url = explode('.',substr(Request::url(),5));
            if($url[0] == 'helper'){
                if(!in_array(Request::action(), ['base', 'login','getAppNewsVersion','getAppPackage'])){
                    $session = Cache::get((string)request()->param('token'));
                    if(isset($session['phone']) AND $session['phone'] == 'test'){
                        if(Request::isPost() && in_array(Request::action(), ['add', 'edit', 'delete','status','renew','shop','pact','order','goods','trade','delivery','wifi'])){
                            die(json_encode(['code' => 0, 'msg' => '体验用户，无权操作！'],JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            }
		}
    }
    
    /**
     * 查找单条记录
     * @param $data
     * @param array $with
     * @return array|static|null
     */
    public static function get($data, $with = [])
    {
        try {
            $query = (new static)->with($with);
            return is_array($data) ? $query->where($data)->find() : $query->find((int)$data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取当前调用来源的应用名称
     * 例如：admin, api, store, task
     * @return string|bool
     */
    protected static function getCalledModule()
    {
        if (preg_match('/app\\\(\w+)/', get_called_class(), $class)) {
            return $class[1];
        }
        return 'common';
    }


    /**
     * 返回错误信息
     * @return string
     */
    public function getError()
    {
        return empty($this->error) ? false : $this->error;
    }
}
