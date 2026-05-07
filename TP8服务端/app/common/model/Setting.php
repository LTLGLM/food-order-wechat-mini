<?php
namespace app\common\model;

use Endroid\QrCode\QrCode as CodeMode;
use Endroid\QrCode\Writer\PngWriter;
use hema\map\Driver as MapDriver;
use hema\alipay\Driver as Alipay;
use hema\wechat\Wxapp;
use hema\wechat\Wechat;
use think\facade\Cache;

/**
 * 站点设置模型
 */
class Setting extends BaseModel
{
    // 定义表名
    protected $name = 'setting';
    protected $createTime = false;
    // 追加字段
    protected $append = [];
    
    /**
     * 设置项描述
     */
    private $describe = [
        'shop' => '门店设置',
        'base' => '站点设置',
        'map' => '地图设置',
        'sms' => '短信平台',
        'h5' => 'H5平台',
		'aliapp' => '支付宝小程序',
		'aliweb' => '支付宝网页应用',
		'wxapp' => '微信小程序',
        'wxapptpl' => '微信小程序订阅消息',
        'wechat' => '微信公众号',
        'subscribe' => '关注回复',//公众号
        'menus' => '公众号菜单',
		'wxpay' => '微信支付',
		'wxtpl' => '微信模板消息',
		'order' => '订单设置',
		'goods' => '商品设置',
		'wifi' => 'WiFi设置',
		'trade' => '交易设置',
		'delivery' => '配送设置',
		'vip' => '会员卡设置',
        'sign' => '签到设置',
        'recharge' => '充值设置',
        'pact' => '预约设置',
    ];

    /**
     * 获取器: 转义数组格式
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 转义成json格式
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 获取指定项设置
     */
    public static function getItem(string $key)
    {
        $data = self::getAll();
        return isset($data[$key]) ? $data[$key]['values'] : [];
    }

    /**
     * 获取设置项信息
     */
    public static function detail(string $key)
    {
        // 筛选条件
        $filter = [
			'key' => $key
		];
        return self::where($filter)->find();
    }

    /**
     * 全局缓存: 系统设置
     */
    public static function getAll()
    {
        $self = new static;
        if (!$data = Cache::get('setting')) {
            $data = array_column($self::select()->toArray(), null, 'key');
            Cache::set('setting', $data);
        }
        return array_merge_multiple($self->defaultData(), $data);
    }

    /**
     * 更新系统设置
     */
    public function edit(string $key, array $values)
    {
        //订单设置
        if($key == 'order'){
            if(!$values = $this->order($values)){
                return false;
            }
        }
        //编辑门店
        if($key == 'shop'){
            if(!$values = $this->shop($values)){
                return false;
            }
        }
        //如果是设置微信小程序订阅消息
        if($key == 'wxapptpl'){
            if(!$values = $this->wxapptpl()){
                return false;
            }
        }
        //如果是配置WIFI
        if($key == 'wifi'){
            if(!$values = $this->wifi($values)){
                return false;
            }
        }
        //公众号菜单
        if($key == 'menus'){
            $wx = new Wechat;
            if(!$wx->creatMenu($values)){
                $this->error = $wx->getError();
                return false;
            }
        }
        //关注公众号回复
        if($key == 'subscribe'){
            $values = $this->subscribe($values);
        }
        $model = self::detail($key) ?: $this;
        // 删除系统设置缓存
        Cache::delete('setting');
        return $model->save([
            'key' => $key,
            'describe' => $this->describe[$key],
            'values' => $values
        ]) !== false;
    }
    /**
     * 订单设置，参数格式化
     */
    private function order($value)
    {
        $tab = [];//初始化小程序端菜单选项
        //初始化数据格式
        $tmp = [
            10 => [
                'text' => '堂食',
                'value' => '10',
                'status' => '0'
            ], 
            20 => [
                'text' => '外卖',
                'value' => '20',
                'status' => '0'
            ],
        ];
        foreach ($value['order_mode'] as $item){
            $tmp[$item]['status'] = '1';
            $tab[] = $tmp[$item];
        }
        $value['order_mode'] = [
            'tab' => $tab, //小程序端选项卡
            'value' => $tmp
        ];
        return $value;
    }
    /**
     * 编辑门店信息
     */
    private function shop(array $data)
    {
        if(!is_phone($data['phone'])){
            $this->error = '联系电话格式错误';
			return false;
        }
        if(!isset($data['logo']) or empty($data['logo'])){
            $this->error = '请上传门店图标';
            return false;
        }
        //生成存储路径
        if(!file_exists('./temp')){
            mkdir('./temp',0777,true);
        }
        //生成小程序聚合码 - 门店码
        $qrcode_path = '/temp/qrcode-shop.png';
        if(!is_file('.' . $qrcode_path)){
            $writer = new PngWriter();
            $code = CodeMode::create(base_url() . 'api/h5/index/scene/shop')->setSize(500);
            $result = $writer->write($code);
            $result->saveToFile('.' . $qrcode_path);
        }
        $data['qrcode']['qrcode'] = $qrcode_path;
        //生成小程序聚合码 - 买单码
        $qrcode_path = '/temp/qrcode-paybill.png';
        if(!is_file('.' . $qrcode_path)){
            $writer = new PngWriter();
            $code = CodeMode::create(base_url() . 'api/h5/index/scene/paybill')->setSize(500);
            $result = $writer->write($code);
            $result->saveToFile('.' . $qrcode_path);
        }
        $data['paybill']['qrcode'] = $qrcode_path;
        
        //生成H5码
        if(is_file('./h5/index.html')){
            //门店
            $h5_path = '/temp/h5-shop.png';
            if(!is_file('.' . $h5_path)){
                $code = CodeMode::create(base_url() . 'api/h5/index/scene/shop')->setSize(500);
                $writer = new PngWriter();
                $result = $writer->write($code);
                $result->saveToFile('.' . $h5_path);
            }
            $data['qrcode']['h5'] = $h5_path;
            //买单
            $h5_path = '/temp/h5-paybill.png';
            if(!is_file('.' . $h5_path)){
                $code = CodeMode::create(base_url() . 'api/h5/index/scene/paybill')->setSize(500);
                $writer = new PngWriter();
                $result = $writer->write($code);
                $result->saveToFile('.' . $h5_path);
            }
            $data['paybill']['h5'] = $h5_path;
        }
        //生成微信小程序码 - 门店码
        $wechat_path = '/temp/wechat-shop.png';
        if(!is_file('.' . $wechat_path)){
            $wx = new Wxapp;
            if(!$wx->getUnlimitedQRCode($wechat_path,'shop')){
                $wechat_path = '';
            }
        }
        $data['qrcode']['weixin'] = $wechat_path;
        //生成微信小程序码 - 买单码
        $wechat_path = '/temp/wechat-paybill.png';
        if(!is_file('.' . $wechat_path)){
            $wx = new Wxapp;
            if(!$wx->getUnlimitedQRCode($wechat_path,'paybill')){
                $wechat_path = '';
            }
        }
        $data['paybill']['weixin'] = $wechat_path;
        //生成支付宝小程序码 - 门店码
        $alipay_path = '/temp/alipay-shop.png';
        if(!is_file('.' . $alipay_path)){
            $alipay = new Alipay('aliapp');
            if(!$alipay->openAppQrcodeCreate('shop',$alipay_path)){
                $alipay_path = '';
            }
        }
        $data['qrcode']['alipay'] = $alipay_path;
        //生成支付宝小程序码 - 买单码
        $alipay_path = '/temp/alipay-paybill.png';
        if(!is_file('.' . $alipay_path)){
            $alipay = new Alipay('aliapp');
            if(!$alipay->openAppQrcodeCreate('paybill',$alipay_path)){
                $alipay_path = '';
            }
        }
        $data['paybill']['alipay'] = $alipay_path;
        //解析坐标地址
        if($data['coordinate'] != ''){
            $map = new MapDriver;
            if(!$location = $map->getLocation($data['coordinate'])){
                $this->error = $map->getError();
                return false;
            }
            $data['province'] = $location['province'];
            $data['city'] = $location['city'];
            $data['district'] = $location['district'];
            $data['poi_id'] = $location['poi_id'];//附近小程序使用该参数
        }
        return $data;
    }
    
    /**
     * 关注回复内容
     */
    private function subscribe($data)
    {
        if($data['type']['value'] != 'text'){
            if(!$material = Material::mediaId($data['content']['media_id'])){
                $this->error = '素材不存在';
                return false; 
            }
            //视频消息
            if($data['type']['value'] == 'video'){
                //获取视频素材内容
                $data['content']['title'] = $material['name'];
                $data['content']['description'] = $material['introduction'];
            }
            //图文消息
            if($data['type']['value'] == 'news'){
                $data['content']['article_count'] = sizeof($material['text']);
                $item = [];
                foreach ($material['text'] as $vo){
                    $item[] = [
                        'title' => $vo['title'],
                        'description' => $vo['digest'],
                        'pic_url' => $vo['url'],
                        'url' => $vo['content_source_url'],
                    ];
                }
                $data['content']['articles'] = $item;
            }
        }
        return $data;
    }
    
    /**
     * 生成wifi二维码
     */
    private function wifi(array $values)
    {
        //生成存储路径
        if(!file_exists('./temp')){
            mkdir('./temp',0777,true);
        }
        //生成小程序聚合码
        $qrcode_path = '/temp/qrcode-wifi.png';
        if(!is_file('.' . $qrcode_path)){
            $writer = new PngWriter();
            $code = CodeMode::create(base_url() . 'api/h5/index/scene/wifi')->setSize(500);
            $result = $writer->write($code);
            $result->saveToFile('.' . $qrcode_path);
        }
        $values['qrcode']['qrcode'] = $qrcode_path;
        //生成微信小程序码
        $wechat_path = '/temp/wechat-wifi.png';
        if(!is_file('.' . $wechat_path)){
            $wx = new Wxapp;
            if(!$wx->getUnlimitedQRCode($wechat_path,'wifi')){
                $wechat_path = '';
            }
        }
        $values['qrcode']['weixin'] = $wechat_path;
        //生成支付宝小程序码
        $alipay_path = '/temp/alipay-wifi.png';
        if(!is_file('.' . $alipay_path)){
            $alipay = new Alipay('aliapp');
            if(!$alipay->openAppQrcodeCreate('wifi',$alipay_path)){
                $alipay_path = '';
            }
        }
        $values['qrcode']['alipay'] = $alipay_path;
        return $values;
    }
    
    /**
     * 餐饮微信订阅消息设置
     */
    private function wxapptpl()
    {
        $values = [];
        $wx = new Wxapp;
        //获取已设置的服务类目
        if(!$result = $wx->getSettingCategories()){
            $this->error = $wx->getError();
            return false;
        }
        $isSet = false;//是否符合设置要求
        //验证是否添加指定的服务类目录
        foreach ($result['data'] as $item){
            //1273类目 = 商业服务- 软件/建站/技术开发
            if($item['id']==1273){
                $isSet = true;
                break;
            }
        }
        if(!$isSet){
            $this->error = '配置失败：该小程序未添加“商业服务-软件/建站/技术开发”服务类目，或未通过审核';
            return false;
        }
        //获取帐号下的模板列表
        if(!$result = $wx->getMessageTemplateList()){
            $this->error = $wx->getError();
            return false;
        }
        //循环删除订阅消息模板
        foreach ($result['data'] as $item){
            if(!$wx->deleteMessageTemplate($item['priTmplId'])){
                $this->error = $wx->getError();
                return false;
            }
        }
        
        //添加商家接单通知
        $tid = '31781';
        //1商家名称，2订单状态，3接单时间
        $kidlist = [1,2,3];
        $desc ='商家接单通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['receive'] = $result['priTmplId'];
        
        //添加骑手取货通知
        $tid = '31780';
        //1商家名称、2订单状态、3接单时间
        $kidlist = [1,2,3];
        $desc ='骑手接单通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['horseman'] = $result['priTmplId'];
        
        //订单配送通知
        $tid = '31339';
        //8商家名称 9订单状态 5配送人、6配送电话 10送出时间
        $kidlist = [8,9,5,6,10];
        $desc ='订单配送通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['delivery'] = $result['priTmplId'];
        
        //提货通知
        $tid = '30804';
        //3提货门店、2状态、6提货码、9提货地点、7联系电话
        $kidlist = [3,2,6,9,7];
        $desc ='提货通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['take'] = $result['priTmplId'];
        
        //退款状态通知
        $tid = '31441';
        //8订单编号、9退款金额、4退款状态、5备注、
        $kidlist = [8,9,4,5];
        $desc ='退款状态通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['refund'] = $result['priTmplId'];
        
        //预约状态通知
        $tid = '30746';//模板编号
        //1姓名、2电话、3预约时间、5预约状态、
        $kidlist = [1,2,3,5];
        $desc ='预约状态通知';
        if(!$result = $wx->addMessageTemplate($tid,$kidlist,$desc)){
            $this->error = $wx->getError();
            return false;
        }
        $values['pact_status'] = $result['priTmplId'];
        
        return $values;
    }

    /**
     * 默认配置
     */
    public function defaultData()
    {
        return [
            'shop' => [
                'key' => 'shop',
                'describe' => '门店设置',
                'values' => [
                    'shop_name' => '我的门店',//门店名称
                    'logo' => base_url() . 'assets/img/hema.png',//门店logo
                    'linkman' => '张三',//联系人
                    'phone' => '15888888888',//联系电话
                    'shop_hours' => '00:00 - 00:00',//营业时间
                    'province' => '江苏省',//详细地址
                    'city' => '徐州市',//详细地址
                    'district' => '鼓楼区',//详细地址
                    'address' => '和信广场',//详细地址
                    'poi_id' => '',//附近地点（附近小程序有效）
                    'coordinate' => '',//门店坐标
                    'business' => base_url() . 'assets/img/no.png',//工商营业执照
                    'license' => base_url() . 'assets/img/no.png',//食品经营许可证
                    'status' => 1,//门店状态(1开启 0关闭)
                    'qrcode' => [
                        'h5' => '',
                        'weixin' => '',
                        'alipay' => '',
                        'qrcode' => '',
                    ],
                    'paybill' => [
                        'h5' => '',
                        'weixin' => '',
                        'alipay' => '',
                        'qrcode' => '',
                    ],
                ],
            ],
            'base' => [
                'key' => 'base',
                'describe' => '基本设置',
                'values' => [
                    'name' => '餐饮点单系统',//网站名称
                    'company' => '',//公司名称
                    'phone' => '',//联系电话
                    'icp' => '',    //备案号
                ],
            ],
            'wechat' => [
                'key' => 'wechat',
                'describe' => '微信公众号',
                'values' => [
                    'app_id' => '', //AppID
                    'app_secret' => '',//AppSecret
                    'url' => base_url() . 'api/task/wechat',//服务器地址(URL)
                    'token' => '',//令牌(Token)
                    'encoding_aes_key' => '',//消息加解密密钥EncodingAESKey
                ]
            ],
            'subscribe' => [
                'key' => 'subscribe',
                'describe' => '关注回复',
                'values' => [
                    'is_open' => [
                        'value' => 1,//是否开启 0=关闭，1=开启
                    ], 
                    'type' => [
                        'value' => 'text', //消息类型 text=文字消息,image=图片消息,news=图文消息,voice=声音消息  
                    ],
                    'content' => [
                        'text' => '感谢您的关注！',//回复文字
                        'media_id' => '',//素材
                    ]
                ]
            ],
            'menus' => [
                'key' => 'menus',
                'describe' => '公众号菜单',
                'values' => [
                /*  0 => [
                        "type" => "view",
                        "name" => "一级菜单",
                        "sub_button" => [
                            0 => [
                                "type" => "click",
                                "name" => "二级菜单",
                                "key" => "关键字"
                            ],
                        ],
                        "url" => base_url()
                    ]*/
                ],
            ],
			'h5' => [
			    'key' => 'h5',
			    'describe' => 'H5平台',
			    'values' => [
			        'order_login' => 1,//下单登录
			    ]
			],
			'wxapp' => [
			    'key' => 'wxapp',
			    'describe' => '微信小程序',
			    'values' => [
			        'app_id' => '',//小程序ID
			        'app_secret' => '',//小程序密钥
			    ]
			],
            'wxpay' => [
                'key' => 'wxpay',
                'describe' => '微信支付',
                'values' => [
                    'mch_id' => '',//商户号
                    'api_key' => '', //APIv3密钥
                    'api_serial_no' => '',//API证书序列号
                    'cert_pem' => '', //apiclient_cert.pem 证书
                    'key_pem' => '', //apiclient_key.pem 密钥
                    'serial_no' => '',//平台证书序列号
                    'certificates' => '',//平台证书
                    'expire_time' => 0,//平台证书到期时间
                ]
            ],
            'aliapp' => [
                'key' => 'aliapp',
                'describe' => '支付宝小程序',
                'values' => [
                    'app_id' => '', //APPID
                    'aes_key' => '',//AES密钥
                    'app_private_key' => '', //应用私钥
                    'alipay_public_key' => '',//支付宝公钥
                ],
            ],
            'aliweb' => [
                'key' => 'aliweb',
                'describe' => '支付宝网页应用',
                'values' => [
                    'app_id' => '', //APPID
                    'app_private_key' => '', //应用私钥
                ]
            ],
			'order' => [
			    'key' => 'order',
			    'describe' => '订单设置',
			    'values' => [
			        'after_pay' => 0,//餐后付款
					'is_order' => 1,//是否启用自动接单
					'is_flavor' => 0,//是否启用口味选项
					'is_people' => 0,//是否启用就餐人数
					'ware_price' => 0,//茶位费用
					'is_scan' => 0,//是否强制扫码下单
					'order_mode' => [
					    'value' => [
                            10 => [
                                'text' => '堂食',
                                'value' => '10',
                                'status' => '1'
                            ], 
                            20 => [
                                'text' => '外卖',
                                'value' => '20',
                                'status' => '1'
                            ],
                        ],
                        'tab' => [
                            0 => [
                                'text' => '堂食',
                                'value' => '10',
                                'status' => '1'
                            ], 
                            1 => [
                                'text' => '外卖',
                                'value' => '20',
                                'status' => '1'
                            ],    
                        ],
				    ],
			    ],
			],
			'goods' => [
			    'key' => 'goods',
			    'describe' => '商品设置',
			    'values' => [
					'is_stock' => 0,//是否启用商品库存
					'sell_out' => 0,//售罄显示
                    'delisting' => 0,//下架显示
			    ],
			],
			'wifi' => [
			    'key' => 'wifi',
			    'describe' => 'WiFi设置',
			    'values' => [
					'ss_id' => '',//WiFi名称
					'ss_key' => '',//WiFi密码
					'qrcode' => [
					    'weixin' => '',
                        'qrcode' => '',
                        'alipay' => '',
					],
			    ],
			],
			'trade' => [
                'key' => 'trade',
                'describe' => '交易设置',
                'values' => [
                    'time' => '10',//任务执行间隔
                    'close_time' => '30',//未支付订单关闭时间
                    'delivery_time' => '30',//已配送订单自动配送完成时间
                    'receive_time' => '30', //配送完毕订单用户自动确认收货时间 
                    'cmt_time' => '120', //收货订单用户自动评价时间
                    'refund_time' => '0',//退款申请订单是否自动完成
                ]
            ],
            'delivery' => [
                'key' => 'delivery',
                'describe' => '配送设置',
                'values' => [
                    'delivery_range' => '3000', //配送范围
                    'free_range' => '0',        //免费配送范围
                    'delivery_price' => '5',    //配送费用
                    'min_price' => '15',        //起送价格
                    'is_delivery' => '0',//是否开启自动配送
                    'company' => 'self',//配送公司，开启自动配送有效。默认self=商家自配
                    'sf_shop_id' => '',//顺丰同城门店ID
                    'dada_shop_id' => '',//达达快送门店ID
                    'make_shop_id' => '',//码科配送门店ID
                    'hmpt_shop_id' => '',//河马跑腿门店ID
                    'shansong_shop_id' => '',//闪送门店ID
                    'uu_shop_id' => '',//UU跑腿门店ID
                ]
            ],
            'wxapptpl' => [
                'key' => 'wxapptpl',
                'describe' => '微信小程序订阅消息',
                'values' => [
                    'receive' => '',//商家接单通知
                    'horseman' => '',//骑手取货通知
                    'delivery' => '',  //订单配送通知
                    'take' => '',  //取餐提醒
                    'refund' => '',  //退款状态通知
                    'pact_status' => '',  //预约状态通知
                ],
            ],
            'wxtpl' => [
                'key' => 'wxtpl',
                'describe' => '微信模板消息',
                'values' => [
                    'news_order' => '',//新订单通知
                    'register_success' => '',//注册成功通知
                    'apply_refund' => '',  //用户申请退款通知
                    'cash_account' => '',  //提现到账通知
                    'apply_cash' => '',  //提现申请通知
                ],
            ],
            'sms' => [
                'key' => 'sms',
                'describe' => '短信平台',
                'values' => [
                    'gateway' => '',     //短信平台
                    'scene' => [
                        'captcha' => 0,//验证码
                    ]
                ]
            ],
            'map' => [
                'key' => 'map',
                'describe' => '地图设置',
                'values' => [
                    'gateway' => '',     //地图平台
                ]
            ],
            'pact' => [
                'key' => 'pact',
                'describe' => '预约设置',
                'values' => [
                    'is_open' => 1,//是否开启
                    'is_print' => 1,//是否打印
                    'range' => 3,//提前预约天数 0为仅限当天
                ],
            ],
            'vip' => [
                'key' => 'vip',
                'describe' => '会员卡设置',
                'values' => [
                    'is_open' => 1,//是否开启
                    'buy_price' => 0,//购买价格
                    'expire_time' => 0,//1一年 0终身，
                    'vip' => [
                        0 => [
                            'card_cover' => base_url() . 'assets/img/vip/v1.png',
                            'name' => 'V1',
                            'discount' => 0,//等级折扣
                            'score' => 0,//所需积分
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ],
                        1 => [
                            'card_cover' => base_url() . 'assets/img/vip/v2.png',
                            'name' => 'V2',
                            'discount' => 95,//等级折扣
                            'score' => 500, 
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ],
                        2 => [
                            'card_cover' => base_url() . 'assets/img/vip/v3.png',
                            'name' => 'V3',
                            'discount' => 90,//等级折扣
                            'score' => 1000, 
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ],
                        3 => [
                            'card_cover' => base_url() . 'assets/img/vip/v4.png',
                            'name' => 'V4',
                            'discount' => 85,//等级折扣
                            'score' => 2000, 
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ],
                        4 => [
                            'card_cover' => base_url() . 'assets/img/vip/v5.png',
                            'name' => 'V5',
                            'discount' => 80,//等级折扣
                            'score' => 5000, 
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ], 
                        5 => [
                            'card_cover' => base_url() . 'assets/img/vip/v6.png',
                            'name' => 'V6',
                            'discount' => 75,//等级折扣
                            'score' => 10000,//所需积分
                            'free_delivery' => 0,//是否免配送费
                            'gift' => []
                        ],
                    ]
                ],
            ],
           'sign' => [
                'key' => 'sign',
                'describe' => '签到设置',
                'values' => [
                    'type' => 'score',//奖励类型 score=积分，money=余额
                    'mode' => 'fixed',//奖励模式 fixed=固定，random=随机
                    'number' => 1,//
                    'plan' => [
                        /*0 => [
                            'days' => 15,//连续天数
                            'number' => 10,//额外奖励
                            'type' => 'score',//奖励类型
                        ],*/
                    ],
                    'describe' => '1. 每次签到赠送1积分
2. 连续签到15天，额外赠送10积分
3. 连续签到30天，额外赠送40积分', //签到说明
                ],
            ],
            'recharge' => [
                'key' => 'recharge',
                'describe' => '充值设置',
                'values' => [
                    'is_open' => 1,     //是否开启
                    'is_custom' => 1,   //是否允许用户自定义金额
                    'describe' => '1. 账户充值仅限微信在线支付方式，充值金额实时到账
2. 账户充值套餐赠送的金额即时到账
3. 账户余额有效期：自充值日起至用完即止
4. 储值金额不可提现、不计利息、不可转赠', //充值说明
                ],
            ],
        ];
    }
}
