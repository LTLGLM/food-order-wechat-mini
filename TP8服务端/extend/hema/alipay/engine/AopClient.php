<?php
namespace hema\alipay\engine;

use app\common\model\Setting;
use hema\alipay\engine\AopEncrypt;

class AopClient
{
    private $alipay_app;//调用接口应用类型 默认aliapp=小程序 aliweb=网页应用
    private $config;//应用配置参数
    private $gatewayUrl = "https://openapi.alipay.com/gateway.do";//网关
    private $format = "json";//返回数据格式
    private $postCharset = "UTF-8";// 表单提交字符集编码
    private $signType = "RSA2";//签名类型
    private $encryptType = "AES";//加密密钥和类型
    private $RESPONSE_SUFFIX = "_response";
    private $ERROR_RESPONSE = "error_response";
    private $SIGN_NODE_NAME = "sign";
    private $apiVersion = "1.0";//api版本
    private $fileCharset = "UTF-8";
    private $alipaySdkVersion = "alipay-sdk-PHP-4.19.101.ALL";
    private $notifyUrl;//回调地址
    private $returnUrl;//返回地址
    private $error;
    
    public function __construct($alipay_app)
    {
        $this->alipay_app = $alipay_app;
        $config = Setting::getItem($alipay_app);
        $this->config = $config;
		/*
        if($alipay_app == 'aliweb'){
            if(empty($config['app_id']) or empty($config['app_private_key'])){
    		    die(json_encode(['code' => 0, 'msg' => '服务端未配置支付宝网页应用参数'],JSON_UNESCAPED_UNICODE));
    		}
        }else{
            if(empty($config['app_id']) or empty($config['app_private_key']) or empty($config['aes_key']) or empty($config['alipay_public_key'])){
    		    die(json_encode(['code' => 0, 'msg' => '服务端未配置支付宝小程序参数'],JSON_UNESCAPED_UNICODE));
    		}
        }
		*/
    }
    
    /**
     * 设置回调地址
     */
    public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl = $notifyUrl;
	}
	
	/**
     * 设置回调地址
     */
    public function setReturnUrl($returnUrl)
	{
		$this->returnUrl = $returnUrl;
	}
	
    /**
     * 接口请求
     * 参数1=请求参数，参数2=请求接口，参数3=是否有bize_content,参数4=是否加解密
     */
    public function execute($apiParams, $method,$httpmethod = "POST", $de = false)
    {
        //组装系统参数
        $sysParams = [
            'app_id' => $this->config['app_id'],
            'method' => $method,//请求接口
            'format' => $this->format,
            'charset' => $this->postCharset,
            'sign_type' => $this->signType,
            'version' => $this->apiVersion,
            'alipay_sdk' => $this->alipaySdkVersion,
            'timestamp' => date("Y-m-d H:i:s"),
        ];
        if(!is_null($this->notifyUrl)){
            $sysParams['notify_url'] = $this->notifyUrl;
        }
        if(!is_null($this->returnUrl)){
            $sysParams['return_url'] = $this->returnUrl;
        }
        $sysParams['biz_content'] = $apiParams;
        $sysParams['biz_content'] = json_encode($sysParams['biz_content'],JSON_UNESCAPED_UNICODE);
        //是否要加密
        if($de){
            $sysParams["encrypt_type"] = $this->encryptType;
            // 执行加密
            $en = new AopEncrypt($this->config['aes_key']);
            $sysParams['biz_content'] = $en->encrypt($sysParams['biz_content']);
        }
        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            if ($sysParamValue != null) {
                $requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
            }
        }
        $requestUrl = substr($requestUrl, 0, -1);
        //发起HTTP请求
        try {
            $resp = $this->result($this->curl($requestUrl, $apiParams),$method);
            if($de){
                //解密
                $de = new AopEncrypt($this->config['aes_key']);
                return json_decode($de->decrypt($resp),true);
            }
            return $resp;
        } catch (Exception $e) {
            $this->error = $method . '接口请求错误！错误代码：' . $e->getCode() . '，错误信息：' .$e->getMessage();
            return false;
        }
    }
    
    /**
     * 页面提交执行方法
     * @param $request 跳转类接口的request
     * @param string $httpmethod 提交方式,两个值可选：post、get;
     * @return 构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
     * @throws Exception
     */
    public function pageExecute($apiParams, $method,$httpmethod = "POST", $de = false)
    {
        //组装系统参数
        $sysParams = [
            'app_id' => $this->config['app_id'],
            'method' => $method,//请求接口
            'format' => $this->format,
            'charset' => $this->postCharset,
            'sign_type' => $this->signType,
            'version' => $this->apiVersion,
            'alipay_sdk' => $this->alipaySdkVersion,
            'timestamp' => date("Y-m-d H:i:s"),
        ];
        if(!is_null($this->notifyUrl)){
            $sysParams['notify_url'] = $this->notifyUrl;
        }
        if(!is_null($this->returnUrl)){
            $sysParams['return_url'] = $this->returnUrl;
        }
        $sysParams['biz_content'] = $apiParams;
        $sysParams['biz_content'] = json_encode($sysParams['biz_content'],JSON_UNESCAPED_UNICODE);
        //是否要加密
        if($de){
            $sysParams["encrypt_type"] = $this->encryptType;
            // 执行加密
            $en = new AopEncrypt($this->config['aes_key']);
            $sysParams['biz_content'] = $en->encrypt($sysParams['biz_content']);
        }
        
        $totalParams = array_merge($apiParams, $sysParams);
        
        //签名
        $totalParams["sign"] = $this->generateSign($totalParams);
        
        if ("GET" == strtoupper($httpmethod)) {
            //value做urlencode
            $preString = $this->getSignContentUrlencode($totalParams);
            //拼接GET请求串
            $requestUrl = $this->gatewayUrl . "?" . $preString;
            return $requestUrl;
        } else {
            //拼接表单字符串
            return $this->buildRequestForm($totalParams);
        }

    }
    
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
    protected function buildRequestForm($para_temp) {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."?charset=".trim($this->postCharset)."' method='POST'>";
        while (list ($key, $val) = $this->fun_adm_each($para_temp)) {
            if (false === $this->checkEmpty($val)) {
                //$val = $this->characet($val, $this->postCharset);
                $val = str_replace("'","&apos;",$val);
                //$val = str_replace("\"","&quot;",$val);
                $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
        return $sHtml;
    }
    protected function fun_adm_each(&$array)
    {
        $res = array();
        $key = key($array);
        if ($key !== null) {
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        } else {
            $res = false;
        }
        return $res;
    }
    private function generateSign($params)
    {
        $params = array_filter($params);
        $params['sign_type'] = $this->signType;
        return $this->sign($this->getSignContent($params));
    }
    
    private function getSignContent($params)
    {
        ksort($params);
        unset($params['sign']);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    
    //此方法对value做urlencode
    public function getSignContentUrlencode($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . urlencode($v);
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
    
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    private function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
    
    private function sign($data)
    {
        $priKey = $this->config['app_private_key'];
        if(empty($priKey)){
            return $priKey;
        }
        $signType = $this->signType;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }
        return base64_encode($sign);
    }
    
    private function curl($url, $postFields = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $postBodyString = "";
        $encodeArray = Array();
        $postMultipart = false;
        if (is_array($postFields) && 0 < count($postFields)) {

            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {

                    $postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
                    $encodeArray[$k] = $this->characet($v, $this->postCharset);
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = new \CURLFile(substr($v, 1));
                }

            }
            unset ($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        if (!$postMultipart) {
            $headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
    
    /**
     * 请求数据验证
     **/
    private function result($result,$method)
    {
        $result = json_decode($result,true);
        $responseNode = str_replace(".", "_", $method . "_response");
        if(isset($result[$responseNode])){
            return $result[$responseNode];
        }
        if(isset($result[$this->ERROR_RESPONSE])){
            $this->error = '错误代码：' . $result[$this->ERROR_RESPONSE]['sub_code'] . '，错误信息：' . $result[$this->ERROR_RESPONSE]['sub_msg'];
            return false;
        }
		$this->error = '未知错误';
		return false;
    }

    public function getError()
    {
        return $this->error;
    }
}