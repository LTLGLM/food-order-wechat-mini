<?php
declare (strict_types=1);

namespace hema\sms;

use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

/**
 * 短信通知模块驱动
 */
class Driver
{
    private $error;
    
    // 当前短信平台 (后台设置)
    private $gateway;

    /**
     * 构造方法
     * Driver constructor.
     */
    public function __construct($gateway)
    {
        // 当前短信平台
        $this->gateway = $gateway;
    }

    /**
     * 发送短信通知
     * @param string $acceptPhone 接收的手机号
     * @param string $templateCode 短信模板ID
     * @param array $templateParams 短信模板参数
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendSms(string $acceptPhone, array $templateParams, string $templateCode=''): bool
    {
        // 实例化EasySms
        $easySmsConfig = Config::getEasySmsConfig($this->gateway);
        $easySms = new EasySms($easySmsConfig);
        if(empty($templateCode)){
            $templateCode = $easySmsConfig['gateways'][$this->gateway]['captcha'];
        }
        try {
            // 执行发送短信
            $result = $easySms->send($acceptPhone, [
                'template' => $templateCode,
                'data' => $this->getSmsTemplateData($templateParams),
            ]);
            // 短信发送成功的错误处理
            $sendStatus = $this->resultHandle($result);
        } catch (NoGatewayAvailableException $e) {
            // 短信发送异常的错误处理
            $sendStatus = false;
            $this->exceptionHandle($e);
        }
        return $sendStatus;
    }
    
    /**
     * 短信发送成功的错误处理
     * @param array $result
     * @return bool
     */
    private function resultHandle(array $result): bool
    {
        // 腾讯云短信错误: 模板ID不正确
        if ($this->gateway === 'qcloud') {
            $response = $result[$this->gateway]['result']['Response']['SendStatusSet'];
            if (isset($response[0]) && isset($response[0]['Code']) && $response[0]['Code'] !== 'Ok') {
                $this->error = '请检查后台短信平台参数和模板ID是否正确';
                return false;
            }
        }
        return true;
    }

    /**
     * 短信发送异常的错误处理
     * @param NoGatewayAvailableException $e
     */
    private function exceptionHandle(NoGatewayAvailableException $e)
    {
        // 短信发送失败
        $err = $e->getLastException();
        if ($err instanceof \GuzzleHttp\Exception\ClientException) {
            $body = (string)$err->getResponse()->getBody();
            $result = json_decode($body, true);
            if (isset($result['Message'])) {
                // $errMsg = $result['Message'];
                $this->error = '请检查后台短信平台的参数设置';
                return;
            }
            if (isset($result['message'])) {
                $this->error = $result['message'];
                return;
            }
        }
//        if ($err instanceof \Overtrue\EasySms\Exceptions\GatewayErrorException) {
//            $this->error = $err->getMessage();
//            return;
//        }
        $this->error = $err->getMessage();
    }

    /**
     * 生成短信模板数据
     * @param array $templateParams
     * @return array
     */
    private function getSmsTemplateData(array $templateParams): array
    {
        if ($this->gateway === 'qcloud') {
            $templateParams = array_values($templateParams);
        }
        return $templateParams;
    }
    
    public function getError()
    {
        return $this->error;
    }
}
