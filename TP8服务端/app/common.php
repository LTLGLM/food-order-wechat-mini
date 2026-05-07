<?php
/**
 * 应用公共函数库文件
 */
use think\facade\Request;

/**
* 获取两坐标之间距离
*/
if (!function_exists('get_distance')) {
    function get_distance($coordinate1, $coordinate2)
    {
        $coordinate1 = explode(',',$coordinate1);
        $coordinate2 = explode(',',$coordinate2);
        $lat1 = $coordinate1[0];
        $lon1 = $coordinate1[1];
        $lat2 = $coordinate2[0];
        $lon2 = $coordinate2[1];
        
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $km = ($miles * 1.609344);//获取公里
        $m = $km * 1000;
        if($m >= 500){
            $text = round($km,1) . 'km';
        }else{
            $text = round($m) . 'm';
        }
        return ['value' => round($m), 'text' => $text];
    }
}

/**
* 地心坐标系 (WGS-84) 与火星坐标系 (GCJ-02) 的转换算法   将WGS-84 坐标转换成GCJ-02 坐标 
*/
if (!function_exists('wgs84_to_gcj02')) {
    function wgs84_to_gcj02($wgs84_lat, $wgs84_lon) {
        $threshold = 0.000000001;
        $a = 6378245.0;
        $ee = 0.00669342162296594323;
     
        $dLat = transformLat($wgs84_lon - 105.0, $wgs84_lat - 35.0);
        $dLng = transformLng($wgs84_lon - 105.0, $wgs84_lat - 35.0);
        $radLat = $wgs84_lat / 180.0 * M_PI;
        $magic = sin($radLat);
        $magic = 1 - $ee * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dLat = ($dLat * 180.0) / (($a * (1 - $ee)) / ($magic * $sqrtMagic) * M_PI);
        $dLng = ($dLng * 180.0) / ($a / $sqrtMagic * cos($radLat) * M_PI);
        $lat = $wgs84_lat + $dLat;
        $lon = $wgs84_lon + $dLng;
        return compact('lon', 'lat');
    }
}

function transformLat($x, $y) {
    $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * sqrt(abs($x));
    $ret += (20.0 * sin(6.0 * $x * M_PI) + 20.0 * sin(2.0 * $x * M_PI)) * 2.0 / 3.0;
    $ret += (20.0 * sin($y * M_PI) + 40.0 * sin($y / 3.0 * M_PI)) * 2.0 / 3.0;
    $ret += (160.0 * sin($y / 12.0 * M_PI) + 320 * sin($y * M_PI / 30.0)) * 2.0 / 3.0;
    return $ret;
}
function transformLng($x, $y) {
    $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * sqrt(abs($x));
    $ret += (20.0 * sin(6.0 * $x * M_PI) + 20.0 * sin(2.0 * $x * M_PI)) * 2.0 / 3.0;
    $ret += (20.0 * sin($x * M_PI) + 40.0 * sin($x / 3.0 * M_PI)) * 2.0 / 3.0;
    $ret += (150.0 * sin($x / 12.0 * M_PI) + 300.0 * sin($x / 30.0 * M_PI)) * 2.0 / 3.0;
    return $ret;
}

/**
* 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换算法 将 GCJ-02 坐标转换成 BD-09 坐标
*/
if (!function_exists('gcj02_to_bd09')) {
     function gcj02_to_bd09($gcj02_lat, $gcj02_lon) {
         $x = $gcj02_lon;
         $y = $gcj02_lat;
         $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * pi());
         $theta = atan2($y, $x) + 0.000003 * cos($x * pi());
         $lon = $z * cos($theta) + 0.0065;
         $lat = $z * sin($theta) + 0.006;
         return compact('lon', 'lat');
     }
}
 
/**
* 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换算法   将 BD-09 坐标转换成GCJ-02 坐标 
*/
if (!function_exists('bd09_to_gcj02')) {
    function bd09_to_gcj02($bd09_lat, $bd09_lon) {
        $x = $bd09_lon - 0.0065;
        $y = $bd09_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * pi());
        $theta = atan2($y, $x) - 0.000003 * cos($x * pi());
        $lon = $z * cos($theta);
        $lat = $z * sin($theta);
        return compact('lon', 'lat');
    }
}

/**
 * 后台菜单格式转换
 */
if (!function_exists('menus')) {
    function menus($menus) 
    {
        $data = [];
        //筛选一级菜单
        foreach($menus as $item){
            if($item['parent'] == '#'){
                $data[$item['id']] = $item;
                //二级菜单
                $second = 0;
                foreach ($menus as $item2){
                    if($item['id'] == $item2['parent']){
                        $data[$item['id']]['submenu'][$second] = $item2;
                        //三级菜单
                        $third = 0;
                        foreach ($menus as $item3){
                            if($item2['id'] == $item3['parent']){
                                if(isset($item2['active'])){
                                    $data[$item['id']]['submenu'][$second]['submenu'][$third] = $item3;
                                    foreach ($menus as $item4){
                                        if($item3['id'] == $item4['parent']){ $data[$item['id']]['submenu'][$second]['submenu'][$third]['urls'][] = $item4['url'];
                                        }
                                    }
                                    $third++;
                                }else{
                                   $data[$item['id']]['submenu'][$second]['urls'][] = $item3['url']; 
                                }
                            }
                        }
                        $second++;
                    }
                }
            }
        }
        return $data;
    }
}

/**
 * 后台菜单格式转换
 */
if (!function_exists('menus_role')) {
    function menus_role($menus,$values) 
    {
        $item = [];
        foreach($values as $vo){
            $arr = explode('-',$vo);
            $str = '';
            for($n=0;$n<sizeof($arr);$n++){
                if($n > 0){
                    $str .= '-' . $arr[$n];
                }else{
                   $str = $arr[$n]; 
                }
                $item[] = $str;
            }
              
        }
        $item = array_unique($item);//去除重复得元素
        $values = array_values($item);//重新排列键值
        $arr = [];
        $url = [];
        foreach($menus as $item){
            foreach($values as $vo){
                if($item['id'] == $vo){
                   $arr[] = $item;
                   if(isset($item['url'])){
                       $url[] = $item['url'];
                   }
               }
            }
        }
        $data = menus($arr);
        $url = array_unique($url);//去除重复得元素
        $url = array_values($url);//重新排列键值
        return [
            'menus' => $data,
            'url' => $url,
        ];
    }
}
/**
 * 生成数字验证码
 */
if (!function_exists('is_json')) {
    function is_json($string) 
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
/**
 * 生成数字验证码
 */
if (!function_exists('get_captcha')) {
    function get_captcha($length=4) 
    {
        $str = '';
        for($n=0;$n<$length;$n++){
            $str .= (string)rand(0,9);
        }
        return $str;
    }
}
/**
 * json_encode
 */
if (!function_exists('hema_json')) {
    function hema_json($data) 
    {
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
}
/**
* 验证手机号是否正确
*/
if (!function_exists('is_phone')) {
    function is_phone($phone) 
    {
        if (!is_numeric($phone)) {
            return false;
        }
        //return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$|^19[\d]{9}$#', $phone) ? true : false;
        return preg_match('#^13[\d]{9}$|^14[\d]{9}$|^15[\d]{9}$|^16[\d]{9}$|^17[\d]{9}$|^18[\d]{9}$|^19[\d]{9}$#', $phone) ? true : false;
    }
}
/**
 * 生成订单号
 */
if (!function_exists('order_no')) {
    function order_no()
    {
        return date('Ymd') . substr(implode('', array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

/**
 * 生成密码hash值
 */
if (!function_exists('hema_hash')) {
    function hema_hash($password)
    {
        return md5(md5($password) . 'hema_salt_SmTRx');
    }
}
/**
 * 获取当前域名及根路径
 */
function base_url()
{
    static $baseUrl = '';
    if (empty($baseUrl)) {
        $request = Request::instance();
        // url协议
        $scheme = $request->scheme();
        // url子目录
        $rootUrl = root_url();
        // 拼接完整url
        $baseUrl = "{$scheme}://" . $request->host() . $rootUrl;
    }
    return $baseUrl;
}
/**
 * 获取当前域名
 */
function domain()
{
    $request = Request::instance();
    return $request->host();
}
/**
 * 获取当前uploads目录访问地址
 */
function uploads_url()
{
    return base_url() . 'uploads/';
}
/**
 * 获取当前的应用名称
 */
function app_name()
{
    return app('http')->getName();
}
/**
 * 获取web根目录
 */
function web_path()
{
    static $webPath = '';
    if (empty($webPath)) {
        $request = Request::instance();
        $webPath = dirname($request->server('SCRIPT_FILENAME')) . DIRECTORY_SEPARATOR;
    }
    return $webPath;
}
/**
 * 获取当前url的子目录路径
 */
function root_url()
{
    static $rootUrl = '';
    if (empty($rootUrl)) {
        $request = Request::instance();
        $subUrl = str_replace('\\', '/', dirname($request->baseFile()));
        $rootUrl = $subUrl . ($subUrl === '/' ? '' : '/');
    }
    return $rootUrl;
}
/**
 * 清空目录 功能同rmdirs()
 */
function deldir($path)
{
    /*
    $path = './temp/';则清空/public/temp 文件夹
    $path = '../temp/';则清空/temp 文件夹
    */
    //如果是目录则继续
    if(is_dir($path)){
        //扫描一个文件夹内的所有文件夹和文件并返回数组
        $p = scandir($path);
        foreach($p as $val){
            //排除目录中的.和..
            if($val !="." && $val !=".."){
                //如果是目录则递归子目录，继续操作
                if(is_dir($path.$val)){
                    //子目录中操作删除文件夹和文件
                    deldir($path.$val.'/');
                    //目录清空后删除空文件夹
                    @rmdir($path.$val.'/');
                }else{
                    //如果是文件直接删除
                    unlink($path.$val);
                }
            }
        }
    }
}
if (!function_exists('rmdirs')) {
    /**
     * 删除文件夹
     * @param string $dirname  目录
     * @param bool   $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}
if (!function_exists('copydirs')) {
    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest   目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
		
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0777, true);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}

/**
 * 下划线转驼峰
 */
function camelize($uncamelized_words, $separator = '_')
{
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
}
/**
 * 驼峰转下划线
 */
function uncamelize($camelCaps, $separator = '_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}
/**
 * 隐藏手机号中间四位 13012345678 -> 130****5678
 * @param string $phone 手机号
 * @return string
 */
if (!function_exists('hide_phone')) {
    function hide_phone(string $phone)
    {
        return substr_replace($phone, '****', 3, 4);
    }
}
/**
 * 隐藏姓名第二位 欧阳振华 -> 欧*振华
 * @param string $name = 姓名
 * @return string
 */
if (!function_exists('hide_name')) {
    function hide_name(string $name)
    {
        return substr_replace($name, '*', 1, 1);
    }
}
/**
 * 隐藏敏感字符
 */
if (!function_exists('substr_cut')) {
    function substr_cut($value)
    {
        $strlen = mb_strlen($value, 'utf-8');
        if ($strlen <= 1) return $value;
        $firstStr = mb_substr($value, 0, 1, 'utf-8');
        $lastStr = mb_substr($value, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', $strlen - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
}
/**
 * 时间戳转换日期
 */
if (!function_exists('format_time')) {
    function format_time($timeStamp)
    {
        return date('Y-m-d H:i:s', $timeStamp);
    }
}
/**
 * 将时间戳转换为日期时间
 * @param int    $time   时间戳
 * @param string $format 日期时间格式
 * @return string
 */
if (!function_exists('datetime')) {
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }
}
/**
 * 左侧填充0
 * @param $value
 * @param $padLength
 * @return string
 */
if (!function_exists('pad_left')) {
    function pad_left($value, $padLength = 2)
    {
        return \str_pad($value, $padLength, "0", STR_PAD_LEFT);
    }
}
/**
 * 重写trim方法 (解决int类型过滤后后变为string类型)
 * @param $str
 * @return string
 */
if (!function_exists('my_trim')) {
    function my_trim($str)
    {
        return is_string($str) ? trim($str) : $str;
    }
}
/**
 * 重写htmlspecialchars方法 (解决int类型过滤后后变为string类型)
 * @param $string
 * @return string
 */
if (!function_exists('my_htmlspecialchars')) {
    function my_htmlspecialchars($string)
    {
        return is_string($string) ? htmlspecialchars($string) : $string;
    }
}
/**
 * 根据指定长度截取字符串
 */
if (!function_exists('str_substr')) {
    function str_substr($str, $length = 30)
    {
        if (strlen($str) > $length) {
            $str = mb_substr($str, 0, $length);
        }
        return $str;
    }
}
/**
 * 文本左斜杠转换为右斜杠
 */
if (!function_exists('convert_left_slash')) {
    function convert_left_slash(string $string)
    {
        return str_replace('\\', '/', $string);
    }
}
/**
 * 将xml转为array
*/
if (!function_exists('_xml_to_arr')) {
    function _xml_to_arr($xml) 
    {
        $res = @simplexml_load_string ( $xml,NULL, LIBXML_NOCDATA );
        $res = json_decode ( json_encode ( $res), true );
        return $res;
    }
}
/**
 * 多维数组合并
 */
function array_merge_multiple($array1, $array2)
{
    $merge = $array1 + $array2;
    $data = [];
    foreach ($merge as $key => $val) {
        if (
            isset($array1[$key])
            && is_array($array1[$key])
            && isset($array2[$key])
            && is_array($array2[$key])
        ) {
            $data[$key] = array_merge_multiple($array1[$key], $array2[$key]);
        } else {
            $data[$key] = isset($array2[$key]) ? $array2[$key] : $array1[$key];
        }
    }
    return $data;
}
/**
 * 判断是否为自定义索引数组
 */
if (!function_exists('is_assoc')) {
    function is_assoc(array $array)
    {
        if (empty($array)) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
/**
 * 多维数组按照指定字段升/降序排列
 */
if (!function_exists('arr_sort')) {
    function arr_sort(array $data, string $field, string $order = 'desc')
    {
        foreach($data as $val){
            $key_arrays[]=$val[$field];
        }
        if($order == 'desc'){
            array_multisort($key_arrays,SORT_DESC,SORT_NUMERIC,$data);
        }else{
            array_multisort($key_arrays,SORT_ASC,SORT_NUMERIC,$data);
        }
        return $data;
    }
}
/**
 * 二维数组排序
 * $desc 是否从大到小排列
 */
if (!function_exists('array_sort')) {
    function array_sort($arr, $keys, $desc = false)
    {
        $key_value = $new_array = array();
        foreach ($arr as $k => $v) {
            $key_value[$k] = $v[$keys];
        }
        if ($desc) {
            arsort($key_value);//降序
        } else {
            asort($key_value);//升序
        }
        reset($key_value);
        foreach ($key_value as $k => $v) {
            if(is_int($k)){
                $new_array[] = $arr[$k];
            }else{
                $new_array[$k] = $arr[$k];
            }
        }
        return $new_array;
    }
}
//去除二维数组重复值,默认重复保留前面的值
/*
  *array 二维数组
  *keyid 需要判断是否重复的项目
*/
if (!function_exists('array_repeat')) {
    function array_repeat($array,$keyid)
    {
        $array =array_values($array);     
        //提取需要判断的项目变成一维数组
        $a = array_tq($array,$keyid);
         
        //去除一维数组重复值
        $a =array_unique($a);
        //提取二维数组项目值
        foreach($array[0] AS$key=>$value)
        {
            $akey[] =$key;
        }
        //重新拼接二维数组
        foreach($akey AS$key=>$value)
        {
            $b = array_tq($array,$value);
            foreach($a AS$key2=>$value2)
            {
                $c[$key2][$value] =$b[$key2];
            }
        }
        return $c;
    }
}
//提取二维数组项目
if (!function_exists('array_tq')) {
    function array_tq($array, $aval="")
    {
        foreach($array AS $key => $value)
        {
            $result[] = $value[$aval];
        }
        return $result;
    }
}
/**
 * POST请求
 * @param  [type] $url     [description]
 * @param  array  $data    [description]
 * @param  array  $headers [description]
 * @return [type]          [description]
 */
if (!function_exists('http_post')) {
    function http_post($url,$data = array(),$headers=array())
    {
        $curl = curl_init();
        if( count($headers) >= 1 ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    
        if (count($data) >= 1 AND !isset($data['media'])){
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        }elseif(count($data) == 0){
            $data = '{}';
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $output;
    }
}
/**
 * array_column 兼容低版本php
 * (PHP < 5.5.0)
 * @param $array
 * @param $columnKey
 * @param null $indexKey
 * @return array
 */
if (!function_exists('array_column')) {
    function array_column($array, $columnKey, $indexKey = null)
    {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
                }
            }
        }
        return $result;
    }
}
/**
 * 获取全局唯一标识符
 */
if (!function_exists('get_guid_v4')) {
    function get_guid_v4($trim = true)
    {
        // Windows
        if (function_exists('com_create_guid') === true) {
            $charid = com_create_guid();
            return $trim == true ? trim($charid, '{}') : $charid;
        }
        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        // Fallback (PHP 4.2+)
        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        $guidv4 = $lbrace .
            substr($charid, 0, 8) . $hyphen .
            substr($charid, 8, 4) . $hyphen .
            substr($charid, 12, 4) . $hyphen .
            substr($charid, 16, 4) . $hyphen .
            substr($charid, 20, 12) .
            $rbrace;
        return $guidv4;
    }
}
/**
 * 写入日志
 */
function write_log($values, $dir)
{
    if (is_array($values))
        $values = print_r($values, true);
    // 日志内容
    $content = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $values . PHP_EOL . PHP_EOL;
    try {
        // 文件路径
        $filePath = $dir . '/logs/';
        // 路径不存在则创建
        !is_dir($filePath) && mkdir($filePath, 0777, true);
        // 写入文件
        return file_put_contents($filePath . date('Ymd') . '.log', $content, FILE_APPEND);
    } catch (\Exception $e) {
        return false;
    }
}