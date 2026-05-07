<?php
namespace hema\sms;

use Overtrue\EasySms\Strategies\OrderStrategy;

/**
 * EasySms配置类
 * Class Config
 */
class Config
{
    /**
     * 生成EasySms的配置项
     * @return array
     */
    public static function getEasySmsConfig($gateway): array
    {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [$gateway],
            ],
        ];
        // 可用的网关配置
        $config['gateways'][$gateway] = \get_addons_config('sms' . $gateway);
        return $config;
    }
}
