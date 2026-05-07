<?php
namespace hema\wechat;

use app\common\model\Setting;
use hema\wechat\AesUtil;
use think\facade\Cache;
use hema\Http;

/**
 * 微信支付
 */
class Pay
{
    private $api_url = 'https://api.mch.weixin.qq.com';//接口域名
    private $version = 'v3'; //接口版本
    private $config; // 微信支付参数
    private $error;
    /**
     * 构造方法
     */
    public function __construct()
    {
        $config = Setting::getItem('wxpay');//获取微信支付参数
        $wxapp = Setting::getItem('wxapp');//获取微信小程序参数
        $config['app_id'] = $wxapp['app_id'];
        $this->config = $config;
    }
    
    /********** V3接口 **********/
    /**
     * H5下单API
     * $out_trade_no=订单号, $total=支付金额，,$attach=订单描述
     *  $profit_sharing=是否分账（有配送费要分账时传递）
     */
    public function h5($out_trade_no,$total,$notify_url,$attach='订单支付',$profit_sharing = false)
    {
        //验证微信支付参数
        if(!$this->validate()){
            return false;
        }
        //直连商户
        $params = [
            'appid' => $this->config['app_id'],//小程序ID
            'mchid' => $this->config['mch_id'],//商户号
			'description' => $attach,//商品描述
			'out_trade_no' => $out_trade_no,//商户订单号
			'attach' => $attach,//附加数据
			'notify_url' => base_url() . $notify_url, //通知地址
			'amount' => [
			    'total' => intval($total * 100),//订单总金额，单位为分
	        ],
			'scene_info' => [
			    'payer_client_ip' => \request()->ip(),//用户终端IP
	        ],
	        /*
	        'h5_info' => [
	            'type' => 'Wap'
	       ]*/
		];
        $params = hema_json($params);
        $url = $this->getUrl('pay/transactions/h5');//直连商户
        $headers = [
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $this->sign($url,'POST',$params),
            'Content-Type:application/json',
            'Accept:application/json',
            'User-Agent:' . $this->config['mch_id'],
        ];
        $result = json_decode(Http::post($url, $params,[],$headers),true);
        if(isset($result['code'])){
			$this->error = 'code：' . $result['code'] . '，msg：' . $result['message'];
			return false;
		}
	    return $result['h5_url'];
    }
    
    /**
     * JSAPI下单API
     * $out_trade_no=订单号, $total=支付金额，$openid=微信用户ID, ,$attach=订单描述
     *  $profit_sharing=是否分账（有配送费要分账时传递）
     */
    public function jsapi($out_trade_no,$total,$openid,$notify_url,$attach='订单支付',$profit_sharing = false)
    {
        //验证微信支付参数
        if(!$this->validate()){
            return false;
        }
        $params = [
            'appid' => $this->config['app_id'],//小程序ID
            'mchid' => $this->config['mch_id'],//商户号
			'description' => $attach,//商品描述
			'attach' => $attach,//附加数据
			'out_trade_no' => $out_trade_no,//商户订单号
			'notify_url' => base_url() . $notify_url, //通知地址
			'amount' => [
			    'total' => intval($total * 100),//订单总金额，单位为分
	        ],
			'scene_info' => [
			    'payer_client_ip' => \request()->ip(),//用户终端IP
	        ],
	        'payer' => [
			    'openid' => $openid, //用户标识Openid
	        ],
		];
	
        $params = hema_json($params);
        $url = $this->getUrl('pay/transactions/jsapi');//直连商户
        $headers = [
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $this->sign($url,'POST',$params),
            'Content-Type:application/json',
            'Accept:application/json',
            'User-Agent:' . $this->config['mch_id'],
        ];
        $result = json_decode(Http::post($url, $params,[],$headers),true);
        if(isset($result['code'])){
			$this->error = 'code：' . $result['code'] . '，msg：' . $result['message'];
			return false;
		}
		if(!isset($result['prepay_id'])){
			$this->error = 'JSAPI下单接口请求失败';
			return false;
		}
		$data = [
		    'appId' => $this->config['app_id'],
	        'timeStamp' => (string)time(),
	        'nonceStr' => $this->nonce(),
	        'package' => 'prepay_id=' . $result['prepay_id'],
	        'signType' => 'RSA',
		    
	    ];
	    $data['paySign'] = $this->paySign($data);
	    return $data;
    }

    /**
     * 支付成功异步通知
     */
    public function notify($Model,$method='edit')
    {
        //接收微信服务器回调的数据流
        if (!$json = file_get_contents('php://input')) {
            $this->returnHttpCode(false);
        }
        // 将服务器返回的json数据转化为数组
        $result = json_decode($json,true);
        //直连商户
        $api_key = $this->config['api_key'];
        //判断平台证书是否过期
    	if($this->config['expire_time'] < time()){
    	    //更新平台证书
    	    if(!$this->certificates()){
    	        $this->returnHttpCode(false,$this->error);//更新失败
    	    }
    	}
        	
        if(!$decrypt = new AesUtil($api_key)){
		    $this->returnHttpCode(false,$decrypt->getError());
		}
        if(!$res = $decrypt->decryptToString($result['resource']['associated_data'], $result['resource']['nonce'], $result['resource']['ciphertext'])){
		    $this->returnHttpCode(false,$decrypt->getError());
		}
		$data = json_decode($res,true);
		// 订单信息
		if(!$order = $Model->payDetail($data['out_trade_no'])){
		    $this->returnHttpCode(false,'订单不存在');
		}
		
        //判断支付状态
        if($data['trade_state'] == 'SUCCESS') {
            if($method == 'add'){
                $Model->updatePayStatus('weixin',$data['transaction_id'],$order);
                Cache::delete($data['out_trade_no']);
            }else{
                // 更新订单状态
                $order->updatePayStatus('weixin',$data['transaction_id']);
            }
            // 返回状态
            $this->returnHttpCode(true);
        }
        // 返回状态
        $this->returnHttpCode(false, '支付失败');
    }
    
    /**
     * 申请退款API
     */
    public function refunds($transaction_id,$out_refund_no,$refund_fee,$total_fee,$notify_url='',$reason='')
    {
        $params = [
            'transaction_id' => $transaction_id,//微信支付订单号
            'out_refund_no' => $out_refund_no,//退款订单号
            'amount' => [
                'refund' => intval($refund_fee * 100), // 退款金额，价格:单位分
                'total' => intval($total_fee * 100), // 订单金额，价格:单位分
                'currency' => 'CNY', //退款币种 只支持人民币：CNY
            ],
        ];
        
        !empty($reason) && $params['reason'] = $reason;//退款原因
        !empty($notify_url) && $params['notify_url'] = base_url() . $notify_url;  // 异步通知地址
        
        $params = hema_json($params);
        $url = $this->getUrl('refund/domestic/refunds');
        $headers = [
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $this->sign($url,'POST',$params),
            'Content-Type:application/json',
            'Accept:application/json',
            'User-Agent:' . $this->config['mch_id'],
        ];
        $result = json_decode(Http::post($url, $params,[],$headers),true);
        return $result;
    }
    /**
    * 退款成功异步通知
    */
    public function refundsNotify($Model)
    {
        //接收微信服务器回调的数据流
        if (!$json = file_get_contents('php://input')) {
            $this->returnHttpCode(false);
        }
        // 将服务器返回的json数据转化为数组
        $result = json_decode($json,true);
        //直连商户
        $api_key = $this->config['api_key'];
        //判断平台证书是否过期
    	if($this->config['expire_time'] < time()){
    	    //更新平台证书
    	    if(!$this->certificates()){
    	        $this->returnHttpCode(false,$this->error);//更新失败
    	    }
    	}
        	
        if(!$decrypt = new AesUtil($api_key)){
		    $this->returnHttpCode(false,$decrypt->getError());
		}
        if(!$res = $decrypt->decryptToString($result['resource']['associated_data'], $result['resource']['nonce'], $result['resource']['ciphertext'])){
		    $this->returnHttpCode(false,$decrypt->getError());
		}
		$data = json_decode($res,true);
		// 订单信息
		if(!$order = $Model->refundDetail($data['out_refund_no'])){
		    $this->returnHttpCode(false,'订单不存在');
		}
		
        if($data['refund_status'] == 'SUCCESS') {
            // 更新订单状态
            $order->updateRefundStatus($data['refund_id']);
            $this->returnHttpCode(true);// 返回状态
        }
        $this->returnHttpCode(false, '退款失败');
    }
    
    /**
     * 获取平台证书列表
     */
    private function certificates()
    {
        $url = $this->getUrl('certificates');
        $headers = [
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $this->sign($url,'GET'),
            'Accept:application/json',
            'User-Agent:https://zh.wikipedia.org/wiki/User_agent',
        ];
        $result = json_decode(Http::get($url,[],[],$headers),true);
        if(isset($result['code'])){
            if($result['code']=='RESOURCE_NOT_EXISTS'){
                return true;
            }
			$this->error = 'code：' . $result['code'] . '，msg：' . $result['message'];
			return false;
		}
		//验证是否获取到了数据
		if(!isset($result['data']) and sizeof($result['data']) == 0){
		    $this->error = '未获取到可用的平台证书';
		    return false;
		}
		$result = $result['data'][0];//获取证书列表中的第一个数据
		$api_key = $this->config['api_key'];
		if(!$decrypt = new AesUtil($api_key)){
		    $this->error = $decrypt->getError();
		    return false;
		}
		if(!$res = $decrypt->decryptToString($result['encrypt_certificate']['associated_data'], $result['encrypt_certificate']['nonce'], $result['encrypt_certificate']['ciphertext'])){
		    $this->error = $decrypt->getError();
		    return false;
		}
		//计算到期时间
		$expire_time = explode('T',$result['expire_time']);
		$expire_time = strtotime($expire_time[0]);
		
		$model = new Setting;
		//更新平台证书
		$this->config['certificates'] = $res;
    	$this->config['serial_no'] = $result['serial_no'];
    	$this->config['expire_time'] = $expire_time;
		$model->edit('wxpay',$this->config); //保存到数据库
		return true;
    }
    
    /**
     * 返回状态给微信服务器
     */
    private function returnHttpCode($is_success = true, $msg = '失败')
    {
        $json = hema_json([
            'code' => $is_success ? 'SUCCESS' : 'FAIL',
            'message' => $is_success ? '成功' : $msg,
        ]);
        if($is_success){
            header('HTTP/1.1 200 OK');
        }else{
            header('HTTP/1.1 404 Not Found');
        }
        die($json);
    }
    
    /**
     * 生成签名
     * $http_method = HTTP请求的方法（GET,POST,PUT
     * serial_no 为你的商户证书序列号
     * $mch_private_key = 是商户API私钥，在商户平台下载的证书文件包含该文件，名称为apiclient_key.pem
     */
    private function sign($url,$http_method,$body='') 
    {
        $timestamp = time(); //时间戳
        $nonce = $this->nonce(); //随机字符串
        $url_parts = parse_url($url);//url解析成数组
        $canonical_url = $url_parts['path'];//url路径
        if(isset($url_parts['query']) and !empty($url_parts['query'])){
            $canonical_url .= '?' . $url_parts['query'];
        }
        $params = $http_method . "\n" .
        $canonical_url . "\n" .
        $timestamp . "\n" .
        $nonce . "\n" .
        $body . "\n"; 
        $mchid = $this->config['mch_id']; //商户号
        $serial_no = $this->config['api_serial_no']; //API证书序列号
        $mch_private_key = $this->config['key_pem']; //API私有证书
        $raw_sign = '';
        openssl_sign($params, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',$mchid, $nonce, $timestamp, $serial_no, $sign);
        return $token;
    }
    
    /**
     * 生成随机字符串
     */
    private function nonce() 
    {
        return md5(uniqid());
    }
    /*
     * 拼接请求域名接口
     */
    private function getUrl($url) 
    {
        return $this->api_url . '/' . $this->version . '/' . $url;
    }
    
    /**
     * 调起支付签名
    */
    private function paySign($data)
    {
        $params = $this->config['app_id'] . "\n" .
        $data['timeStamp'] . "\n" .
        $data['nonceStr'] . "\n" .
        $data['package'] . "\n"; 
        $private_key = $this->config['key_pem']; //API私有证书
        $raw_sign = '';
        openssl_sign($params, $raw_sign, $private_key, 'sha256WithRSAEncryption');
        return base64_encode($raw_sign);
    }
    /**
     * 验证参数配置
     */
    private function validate()
    {
        $config = $this->config;
        if(empty($config['app_id'])){
            $this->error = '微信小程序 APP_ID 不可为空';
            return false;
        }
        if(empty($config['mch_id'])){
            $this->error = '微信支付商户号 MCH_ID 不可为空';
            return false;
        }
        if(empty($config['api_serial_no'])){
            $this->error = '微信支付API证书序列号不可为空';
            return false;
        }
        if(empty($config['key_pem'])){
            $this->error = '微信支付API证书apiclient_key.pem不可为空';
            return false;
        }
        return true;
    }
    public function getError()
    {
        return $this->error;
    }

}