<?php
namespace hema\storage;

use hema\storage\engine\Local;
use addons\aliyun\library\Aliyun;
use addons\qcloud\library\Qcloud;
use addons\qiniu\library\Qiniu;

/**
 * 存储模块驱动
 */
class Driver
{
    private $engine;    // 当前引擎类
    private $addons = [ //扩展插件
        'qiniu' => '七牛云存储',
        'aliyun' => '阿里云存储',
        'qcloud' => '腾讯云存储'
    ];
    
    /**
     * 存储引擎类列表
     */
    const ENGINE_CLASS_LIST = [
        'local' => Local::class,
        'qiniu' => QINIU::class,
        'aliyun' => Aliyun::class,
        'qcloud' => Qcloud::class
    ];

    /**
     * 构造方法
     * @param null|string $name 扩展插件名称
     */
    public function __construct($name='local')
    {
        $config = null;
        if($name != 'local'){
           if(isset($this->addons[$name])){
                // 实例化当前引擎
                if(!$config = get_addons_config($name)){
                    die(json_encode(['code' => 0, 'msg' => '未安装《' . $this->addons[$name] .'》插件'],JSON_UNESCAPED_UNICODE));
                }
            }else{
                die(json_encode(['code' => 0, 'msg' => '不支持的插件引擎'],JSON_UNESCAPED_UNICODE));
            } 
        }
        $class = self::ENGINE_CLASS_LIST[$name];
        $this->engine = new $class($name, $config);
    }

    /**
     * 设置上传的文件信息
     * @param string $name
     * @return mixed
     */
    public function setUploadFile($name = 'iFile')
    {
        return $this->engine->setUploadFile($name);
    }

    /**
     * 设置上传的文件信息
     * @param string $filePath
     * @return mixed
     */
    public function setUploadFileByReal(string $filePath)
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 设置上传文件的验证规则
     * @param array $rules
     * @return mixed
     */
    public function setValidationScene(array $rules = [])
    {
        return $this->engine->setValidationScene($rules);
    }

    /**
     * 执行文件上传
     */
    public function upload()
    {
        return $this->engine->upload();
    }

    /**
     * 执行文件删除
     * @param string $filePath
     * @return mixed
     */
    public function delete(string $filePath)
    {
        return $this->engine->delete($filePath);
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 返回保存的文件信息
     * @return mixed
     */
    public function getSaveFileInfo()
    {
        return $this->engine->getSaveFileInfo();
    }

}
