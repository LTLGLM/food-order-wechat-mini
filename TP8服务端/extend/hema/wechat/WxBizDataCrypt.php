<?php
namespace hema\wechat;

/**
 * 对微信小程序用户加密数据的解密示例代码.
 */

class WxBizDataCrypt
{
    //private $appid; //小程序的appid
	private $sessionKey;
	
	private $OK = 0;
	private $IllegalAesKey = -41001;	//encodingAesKey 非法
	private $IllegalIv = -41002;
	private $IllegalBuffer = -41003;	//aes 解密失败
	private $DecodeBase64Error = -41004;//解密后得到的buffer非法

	/**
	 * 构造函数
	 * @param $sessionKey string 用户在小程序登录后获取的会话密钥
	 */
	public function __construct(string $sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}

	/**
	 * 检验数据的真实性，并且获取解密后的明文.
	 * @param $encryptedData string 加密的用户数据
	 * @param $iv string 与用户数据一同返回的初始向量
	 * @param $data string 解密后的原文
     *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function decryptData( $encryptedData, $iv, &$data )
	{
		if (strlen($this->sessionKey) != 24) {
			return $this->IllegalAesKey;
		}
		$aesKey=base64_decode($this->sessionKey);
		if (strlen($iv) != 24) {
			return $this->IllegalIv;
		}
		$aesIV=base64_decode($iv);
		$aesCipher=base64_decode($encryptedData);
		$result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
		$dataObj= json_decode($result);
		if( $dataObj  == NULL )
		{
			return $this->IllegalBuffer;
		}
		/*
		if( $dataObj->watermark->appid != $this->appid )
		{
			return $this->IllegalBuffer;
		}
		*/
		$data = $result;
		return $this->OK;
	}

}