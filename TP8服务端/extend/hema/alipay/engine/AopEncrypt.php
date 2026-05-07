<?php
namespace hema\alipay\engine;
/**
 *   加密工具类
 *
 * User: jiehua
 * Date: 16/3/30
 * Time: 下午3:25
 * 服务端解密时，需使用商家小程序 AES 密钥。

调试小程序模板时，使用模板 AES 密钥。
 */

class AopEncrypt
{
    private $aes_key;
    private $error;
	/**
	 * 构造函数
	 */
	public function __construct($aes_key)
	{
	    $this->aes_key = trim($aes_key);
	}
    /**
     * 加密方法
     * @param string $str
     * @return string
     */
    public function encrypt($str)
    {
        //AES, 128 模式加密数据 CBC
        $screct_key = base64_decode($this->aes_key);
        $str = trim($str);
        $str = $this->addPKCS7Padding($str);
    
        //设置全0的IV
    
        $iv = str_repeat("\0", 16);
        $encrypt_str = openssl_encrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        return base64_encode($encrypt_str);
    }
    
    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    public function decrypt($str)
    {
        //AES, 128 模式加密数据 CBC
        $str = base64_decode($str);
        $screct_key = base64_decode($this->aes_key);
        //设置全0的IV
        $iv = str_repeat("\0", 16);
        $decrypt_str = openssl_decrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        $decrypt_str = $this->stripPKSC7Padding($decrypt_str);
        return $decrypt_str;
    }
    
    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    public function addPKCS7Padding($source)
    {
        $source = trim($source);
        $block = 16;
    
        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }
    
    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    public function stripPKSC7Padding($source)
    {
        $char = substr($source, -1);
        $num = ord($char);
        if ($num == 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }
    
    public function getError()
    {
        return $this->error;
    }
    
}