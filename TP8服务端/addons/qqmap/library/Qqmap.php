<?php
namespace addons\qqmap\library;

use hema\map\engine\Basics;
use hema\Http;

/**
 * 腾讯地图驱动类
*/
class Qqmap extends Basics
{
	private $api_url = 'https://apis.map.qq.com';
    private $api_version = 'v1';
	
	/**
     * 计算距离
     * $from 起点，$to 终点
     * $mode 计算方式：driving（驾车）、walking（步行）bicycling：自行车
     */
    public function getDistance(string $from, string $to, string $mode='bicycling')
    {
        $key = $this->config['key'];
        $url = $this->api_url . '/ws/distance/'.$this->api_version.'/matrix';
        $queryarr = [
            'mode' => $mode,
            'from' => $from,
            'to' => $to,
            'key' => $key            
        ];
        $result = json_decode(Http::get($url, $queryarr),true);
        if ($result['status']!=0) {
            $this->error = 'code：'.$result['status'].'，msg：'.$result['message'];
            return false;
        }
        return $result['result']['rows'][0]['elements'];
        /*
        返回数组：[{distance=米,  duration=秒}]
        distance：起点到终点的距离，单位：米
        duration：表示从起点到终点的结合路况的时间，秒为单位
         */
    }

    /**
     * 逆地址解析(坐标位置描述)
     */
    public function getLocation(string $location)
    {
        $key = $this->config['key'];
        if(empty($key)){
            $this->error = '未配置腾讯地图KEY或未安装《腾讯地图》插件';
            return false; 
        }
        $url = $this->api_url . '/ws/geocoder/'.$this->api_version;
        $queryarr = [
            'location' => $location,
            'get_poi' => 1,
            'key' => $key            
        ];
        $result = json_decode(Http::get($url, $queryarr),true);
        if ($result['status'] != 0) {
            $this->error = 'code：'.$result['status'].'，msg：'.$result['message'];
            return false;
        }
        $result['result']['address_component']['poi_id'] = '';
        //获取poi_id
        if(isset($result['result']['pois'][0])){
            $result['result']['address_component']['poi_id'] = $result['result']['pois'][0]['id'];
        }
        return $result['result']['address_component'];
    }

    /**
     * IP定位
     */
    public function getIp($ip='')
    {
        empty($ip) && $ip = \request()->ip();
        $key = $this->config['key'];
        $url = $this->api_url . '/ws/location/'.$this->api_version.'/ip';
        $queryarr = [
            'key' => $key,
            'ip' => $ip
        ];
        $result = json_decode(Http::get($url, $queryarr),true);
        if ($result['status'] != 0) {
            //获取失败，返回模拟位置
            return [
                'ip' => '42.192.66.119',
                'location' => [
                    'lat' => '31.23037',
                    'lng' => '121.4737' 
                ],
                'ad_info' => [
                    'nation' => '中国',
                    'province' => '上海市',
                    'city' => '上海市',
                    'district' => '',
                    'adcode' => '310000'
                ]
            ];
        }
        return $result['result'];
    }
}
