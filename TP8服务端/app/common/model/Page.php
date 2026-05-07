<?php
namespace app\common\model;

/**
 * 小程序diy页面模型
 */
class Page extends BaseModel
{
    //定义表名
    protected $name = 'page';
    
    // 定义主键
    protected $pk = 'page_id';
    
    // 追加字段
    protected $append = [];
	
	/**
     * 页面类型
     */
    public function getPageTypeAttr($value)
    {
        $status = [10 => '默认首页', 20 => '自定义页'];
        return ['text' => $status[$value], 'value' => $value];
    }
    /**
     * 格式化页面数据（读取的时候对数据转换）
     */
    public function getPageDataAttr($json)
    {
        $array = json_decode($json, true);
        return compact('array', 'json');
    }
    /**
     * 自动转换data为json格式(修改器，保存de时候操作)
     */
    public function setPageDataAttr($value)
    {
        return json_encode($value ?: ['items' => []]);
    }
    /**
     * 获取页面列表
     */
    public function getList()
    {
        // 排序规则
        $sort = [];
        $sort = ['page_id' => 'desc'];
        // 执行查询
        return $this->order($sort)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
	/**
     * 获取默认首页详情
     */
    public static function getHome()
    {
        return self::where(['page_type' =>10])->find();
    }
    
    /**
     * 添加
     */
    public function add(array $page_data)
    {
        for($n=0;$n<sizeof($page_data['items']);$n++){
            if($page_data['items'][$n]['type'] == 'richText'){
                $page_data['items'][$n]['params']['content'] = htmlspecialchars_decode($page_data['items'][$n]['params']['content']);
            }
        }
		$data['page_type'] = 20;
		$data['page_data'] = $page_data;
        return $this->save($data);
    }
    /**
     * 更新页面数据
     */
    public function edit(array $page_data)
    {
        for($n=0;$n<sizeof($page_data['items']);$n++){
            if($page_data['items'][$n]['type'] == 'richText'){
                $page_data['items'][$n]['params']['content'] = htmlspecialchars_decode($page_data['items'][$n]['params']['content']);
            }
        }
        return $this->save(compact('page_data')) !== false;
    }
    /**
     * 删除
     */
    public function remove()
    {
        //判断是否批量删除门店
        if($this->count() == 1){
            $this->error = '至少保留一个页面';
            return false;
        }
        return $this->delete();
    }
    /**
     * 设置默认首页
     */
	public function status()
    {
		//取消原来的默认首页
		$this->where('page_type',10)->update(['page_type' => 20]);
		//设置新首页
		return $this->save(['page_type' => 10]) !== false;
    }
    /**
     * 新增小程序首页diy默认设置
     */
    public function insertDefault()
    {
        $page_data = [
			'page' => [
				'type' => 'page',
				'name' => '页面设置',
				'params' => [
					'name' => '默认首页',
					'title' => '我的小程序',
					'share_title' => '分享标题',
					'share_image' => base_url().'assets/img/diy/banner_01.jpg'
				],
				'style' => [
					'titleTextColor' => 'black',
					'titleBackgroundColor' => '#ffffff'
				]
			],
			'tabbar' => [
				'type' => 'tabbar',
				'name' => '导航设置',
				'list' => [
					[
						'text' => '首页', //标题
						'selectedIconPath' =>  'home_on.png', //选中状态
						'iconPath' => 'home.png', //未选中状态
						//'pagePath' => 'index/index',
						'visible' => '1',
					],
					[
						'text' => '点单', //标题
						'selectedIconPath' =>  'dot_on.png', //选中状态
						'iconPath' => 'dot.png', //未选中状态
						//'pagePath' => 'shop/index',
						'visible' => '1',
					],
					[
						'text' => '订单', //标题
						'selectedIconPath' =>  'order_on.png', //选中状态
						'iconPath' => 'order.png', //未选中状态
						//'pagePath' => 'order/index',
						'visible' => '1',
					],
					[
						'text' => '商城', //标题
						'selectedIconPath' =>  'exchange_on.png', //选中状态
						'iconPath' => 'exchange.png', //未选中状态
						//'pagePath' => 'exchange/index',
						'visible' => '1',
					],
					[
						'text' => '我的', //标题
						'selectedIconPath' =>  'user_on.png', //选中状态
						'iconPath' => 'user.png', //未选中状态
						//'pagePath' => 'user/index',
						'visible' => '1',
					]
				],			
				'style' => [
					'borderStyle' => 'white',	//上边框颜色 仅支持 black/white
					'color' => '#c0c4cc',	//未激活时的颜色
					'selectedColor' => '#303133', //激活时的颜色
				    'backgroundColor' => '#ffffff',//背景色
					//'backgroundImage' => '',//图片背景,优先于背景颜色
					//'backgroundRepeat' => '',//背景图平铺方式。repeat：背景图片在垂直方向和水平方向平铺；repeat-x：背景图片在水平方向平铺，垂直方向拉伸；repeat-y：背景图片在垂直方向平铺，水平方向拉伸；no-repeat：背景图片在垂直方向和水平方向都拉伸。 默认使用 no-repeat
				]
			],
			'items' => [
				[
    				'name' => '点单组',
    				'type' => 'order',
    				'style' => [
    				    'marginTop' => '50',
    					'radius' => '10',//圆角
    					'bgColor' => '#ffffff',
    				],
    				'params' => [
    				    'number' => 2,//显示数量  
    				    'is_border' => 1,//显示边框
    				],
    				'data' => [
    					[
    					    'order_mode' => 10,
    						'title' => '堂食',
    						'text' => 'Hall food',
    					],
    					[
    					    'order_mode' => 20,
    						'title' => '外卖',
    						'text' => 'take-out food',
    					],
    				]
    			],
				[
					'name' => '辅助空白',
    				'type' => 'blank',
    				'style' => [
    					'height' => '20',
    				]
				],
				[
					'name' => '商品组',
    				'type' => 'goods',
    				'params' => [
    					'source' => 'auto', //商品来源，auto=自动选择，choice=手动选择
    					'auto' => [	//自动选择配置
    						//'category' => 0, //分类编号
    						'goodsSort' => 'all',	//商品排序，all=综合，sales=销量，new=新品,recommend推荐
    						'showNum' => 10	//显示数量
    					],
    					'title' => '商品推荐',
				        'is_border' => 1,
    				],
    				'style' => [
    				    'radius' => '10',
    					'bgColor' => '#ffffff', //背景颜色
    					'display' => 'slide', 	//显示类型，list=列表平铺，slide=横向滑动
    					'column' => '2',	//分列数量，1=单列，2=双列，3=三列
    					'show' => [	//显示内容
    						'goodsName' => 'true',	//商品名称
    						'goodsPrice' => 'true',	//商品价格
    						'linePrice' => 'true',	//划线价格
    						'sellingPoint' => 'true',//商品卖点
    						'goodsSales' => 'true'	//商品销量
    					]
    				],
    				'defaultData' => [	//默认数据
    					[
    						'goods_name' => '此处显示商品名称',
    						'image' => base_url().'assets/img/diy/goods_01.png',
    						'goods_price' => '99.00',
    						'line_price' => '139.00',
    						'selling_point' => '此款商品美观大方 不容错过',
    						'goods_sales' => '100'
    					],
    					[
    						'goods_name' => '此处显示商品名称',
    						'image' => base_url().'assets/img/diy/goods_01.png',
    						'goods_price' => '99.00',
    						'line_price' => '139.00',
    						'selling_point' => '此款商品美观大方 不容错过',
    						'goods_sales' => '100'
    					],
    					[
    						'goods_name' => '此处显示商品名称',
    						'image' => base_url().'assets/img/diy/goods_01.png',
    						'goods_price' => '99.00',
    						'line_price' => '139.00',
    						'selling_point' => '此款商品美观大方 不容错过',
    						'goods_sales' => '100'
    					],
    					[
    						'goods_name' => '此处显示商品名称',
    						'image' => base_url().'assets/img/diy/goods_01.png',
    						'goods_price' => '99.00',
    						'line_price' => '139.00',
    						'selling_point' => '此款商品美观大方 不容错过',
    						'goods_sales' => '100'
    					]
    				],
    				'data' => []
				],
				[
					'name' => '辅助空白',
    				'type' => 'blank',
    				'style' => [
    					'height' => '20',
    				]
				],
				[
					'name' => '导航组',
    				'type' => 'navBar',
    				'style' => [
    				    'radius' => '10',
    				    'width' => '80',
    				    'height' => '80',
    					'bgColor' => '#ffffff',
    				],
    				'params' => [
    				    'rowsNum' => 4,//显示数量
    				    'is_border' => 1,//显示边框
    				],
    				'data' => [
    					[
    						'image' => base_url().'assets/img/diy/g1.png',
    						'url' => 'user/wallet/index',
    						'text' => '充值',
    						'color' => '#999999'
    					],
    					[
    						'image' => base_url().'assets/img/diy/g2.png',
    						'url' => 'user/sign/index',
    						'text' => '签到',
    						'color' => '#999999'
    					],
    					[
    						'image' => base_url().'assets/img/diy/g4.png',
    						'url' => 'user/pact/index',
    						'text' => '预约',
    						'color' => '#999999'
    					],
    					[
    						'image' => base_url().'assets/img/diy/g8.png',
    						'url' => 'user/vip/index',
    						'text' => '会员',
    						'color' => '#999999'
    					],
    				]
				],
			]				
        ];
        return json_encode($page_data);
    }
	
	/**
     * 首页diy模板
     */
    public static function temp()
    {
		$temp['array'] = [
			//图片轮播
			'banner' => [
				'name' => '图片轮播',
				'type' => 'banner',
				'style' => [
				    'height' => '300', //轮播图组件高度，单位rpx
					'radius' => '10', //轮播图圆角值，单位rpx
					'effect3d' => '0', //是否开启3D效果
					'showTitle' => 'false', //是否显示标题文字
					'indicatorMode' => 'line',	//line	dot
					'indicatorStyle' => 'bottom', //bottom，left，right
					'marginLr' => '0', //左右边距，单位rpx
				],
				'params' => [
					"autoplay" => 'true', //是否自动轮播
					"circular" => 'true',	//	是否衔接播放
					"indicator" => 'true',	//	面板指示
					"interval" => '3000', //自动轮播时间间隔，单位ms
					"duration" => '300',	//	切换一张轮播图所需的时间，单位ms
				],
				'data' => [
					[
					    "type" => 'image',
						"image" => base_url() . 'assets/img/diy/banner_01.jpg',
						"title" => 'banner_01',
						"url" => 'shop/index',
						"poster" => base_url() . 'assets/img/diy/video_poster.png'
					],
					[
					    "type" => 'image',
						"image" => base_url() . 'assets/img/diy/banner_02.jpg',
						"title" => 'banner_01',
						"url" => 'shop/index',
						"poster" => base_url() . 'assets/img/diy/video_poster.png'
					],
					[
					    "type" => 'image',
						"image" => base_url() . 'assets/img/diy/banner_01.jpg',
						"title" => 'banner_01',
						"url" => 'shop/index',
						"poster" => base_url() . 'assets/img/diy/video_poster.png'
					],
				]
			],
			//单图组
			'imageSingle' => [
				'name' => '单图组',
				'type' => 'imageSingle',
				'style' => [
					'radius' => '10',//圆角
					'marginT' => '20',//多个间隔距离
					'bgColor' => '#f7f7f7'
				],
				'data' => [
					[
						'image' => base_url().'assets/img/diy/banner_01.jpg',
						'url' => 'index/index'
					]
				]
			],
			//图片橱窗
			'window' => [
				'name' => '图片橱窗',
				'type' => 'window',
				'style' => [
					'radius' => '10',//圆角
					'marginT' => '20',//多个间隔距离
					'bgColor' => '#f7f7f7',
					'layout' => '2',//样式
				],
				'data' => [
					[
						'image' => base_url().'assets/img/diy/window_01.jpg',
						'url' => 'index/index'
					],
					[
						'image' => base_url().'assets/img/diy/window_02.jpg',
						'url' => 'index/index'
					],
					[
						'image' => base_url().'assets/img/diy/window_03.jpg',
						'url' => 'index/index'
					],
					[
						'image' => base_url().'assets/img/diy/window_04.jpg',
						'url' => 'index/index'
					]
				],
			],
			//视频组
			'video' => [
				'name' => '视频组',
				'type' => 'video',
				'params' => [
				    'autoplay' => 0,//是否自动播放
				    'loop' => 0,//是否循环播放
				    'muted' => 0,//是否静音播放
					'src' => 'http://wxsnsdy.tc.qq.com/105/20210/snsdyvideodownload?filekey=30280201010421301f0201690402534804102ca905ce620b1241b726bc41dcff44e00204012882540400',
					'poster' => base_url().'assets/img/diy/video_poster.png',
				],
				'style' => [
					'height' => '190',
				]
			],
			//公告组
			'notice' => [
				'name' => '公告组',
				'type' => 'notice',
				'params' => [
					'direction' => 'row', //row(水平滚动)	column(垂直滚动)
					'step' => 0,	//水平滚动是否开启步进效果
					'is_border' => 1,
				],
				'style' => [
				    'radius' => '10',
				],
				'data' => [
				    [
						'text' => '这里是第一条自定义公告内容',
						'url' => 'index/index'
					],
				]
			],
			//商品组
			'goods' => [
				'name' => '商品组',
				'type' => 'goods',
				'params' => [
					'source' => 'auto', //商品来源，auto=自动选择，choice=手动选择
					'auto' => [	//自动选择配置
						//'category' => 0, //分类编号
						'goodsSort' => 'all',	//商品排序，all=综合，sales=销量，new=新品,recommend推荐
						'showNum' => 6	//显示数量
					],
				    'title' => '商品推荐',
				    'is_border' => 1,
				],
				'style' => [
				    'radius' => '10',
					'bgColor' => '#ffffff', //背景颜色
					'display' => 'list', 	//显示类型，list=列表平铺，slide=横向滑动
					'column' => '2',	//分列数量，1=单列，2=双列，3=三列
					'show' => [	//显示内容
						'goodsName' => 'true',	//商品名称
						'goodsPrice' => 'true',	//商品价格
						'linePrice' => 'true',	//划线价格
						'sellingPoint' => 'true',//商品卖点
						'goodsSales' => 'true'	//商品销量
					]
				],
				'defaultData' => [	//默认数据
					[
						'goods_name' => '此处显示商品名称',
						'image' => base_url().'assets/img/diy/goods_01.png',
						'goods_price' => '99.00',
						'line_price' => '139.00',
						'selling_point' => '此款商品美观大方 不容错过',
						'goods_sales' => '100'
					],
					[
						'goods_name' => '此处显示商品名称',
						'image' => base_url().'assets/img/diy/goods_01.png',
						'goods_price' => '99.00',
						'line_price' => '139.00',
						'selling_point' => '此款商品美观大方 不容错过',
						'goods_sales' => '100'
					],
					[
						'goods_name' => '此处显示商品名称',
						'image' => base_url().'assets/img/diy/goods_01.png',
						'goods_price' => '99.00',
						'line_price' => '139.00',
						'selling_point' => '此款商品美观大方 不容错过',
						'goods_sales' => '100'
					],
					[
						'goods_name' => '此处显示商品名称',
						'image' => base_url().'assets/img/diy/goods_01.png',
						'goods_price' => '99.00',
						'line_price' => '139.00',
						'selling_point' => '此款商品美观大方 不容错过',
						'goods_sales' => '100'
					]
				],
				'data' => [	//选择数据
					[
						'goods_name' => '此处显示商品名称',
						'image' => base_url().'assets/img/diy/goods_01.png',
						'goods_price' => '99.00',
						'line_price' => '139.00',
						'selling_point' => '此款商品美观大方 不容错过',
						'goods_sales' => '100'
					]
				]
			],
			//在线客服
			'service' => [
				'name' => '在线客服',
				'type' => 'service',
				'params' => [
					'type' => 'chat',//chat=客服，phone=拨打电话
					'image' => base_url().'assets/img/diy/service.png',
					'phone_num' => ''
				],
				'style' => [
					'right' => '20',
					'bottom' => '20',
					'opacity' => '100'
				]
			],
			//关注公众号
			'officialAccount' => [
				'name' => '关注公众号',
				'type' => 'officialAccount',
				'params' => [],
				'style' => []
			],
			//富文本
			'richText' => [
				'name' => '富文本',
				'type' => 'richText',
				'params' => [
				    'title' => '栏目标题',
				    'is_border' => 1,
					'content' => '这里是文本的内容'
				],
				'style' => [
				    'radius' => '10',
					'bgColor' => '#ffffff'
				]
			],
			//辅助空白
			'blank' => [
				'name' => '辅助空白',
				'type' => 'blank',
				'style' => [
					'height' => '20',
				]
			],
			//辅助线
			'guide' => [
				'name' => '辅助线',
				'type' => 'guide',
				'style' => [
				    'marginLr' => '20',
					'bgColor' => '#f7f7f7',
					'style' => 'solid',
					'height' => '1',
					'color' => '#f7f7f7',
					'paddingTb' => '20'
				]
			],
			//直播间
			'liveRoom' => [
				'name' => '直播间',
				'type' => 'liveRoom',
				'params' => [
					'image' => base_url().'assets/img/diy/live_room.png'
				],
				'style' => [
					'right' => '1',
					'bottom' => '10',
					'opacity' => '100'
				]
			],
			//点单
			'order' => [
				'name' => '点单组',
				'type' => 'order',
				'style' => [
					'radius' => '10',//圆角
					'bgColor' => '#ffffff',
				],
				'params' => [
				    'number' => 2,//显示数量
				    'is_border' => 1,//显示边框
				],
				'data' => [
					[
					    'order_mode' => 10,
						'title' => '堂食',
						'text' => 'Hall Food',
					],
					[
					    'order_mode' => 20,
						'title' => '外卖',
						'text' => 'Take Out',
					],
				]
			],
			//导航组
			'navBar' => [
				'name' => '导航组',
				'type' => 'navBar',
				'style' => [
				    'radius' => '10',
				    'width' => '80',
				    'height' => '80',
					'bgColor' => '#ffffff',
				],
				'params' => [
				    'title' => '推荐栏目',
				    'rowsNum' => 4,//显示数量
				    'is_border' => 1,//显示边框
				],
				'data' => [
					[
						'image' => base_url().'assets/img/diy/g1.png',
						'url' => 'user/wallet/index',
						'text' => '充值',
						'color' => '#999999'
					],
					[
						'image' => base_url().'assets/img/diy/g2.png',
						'url' => 'user/sign/index',
						'text' => '签到',
						'color' => '#999999'
					],
					[
						'image' => base_url().'assets/img/diy/g4.png',
						'url' => 'user/pact/index',
						'text' => '预约',
						'color' => '#999999'
					],
					[
						'image' => base_url().'assets/img/diy/g8.png',
						'url' => 'user/vip/index',
						'text' => '会员',
						'color' => '#999999'
					],
				]
			],
			//栏目标题
			'columnTitle' => [
				'name' => '栏目标题',
				'type' => 'columnTitle',
				'params' => [
					'title' => '标题名称',//左边主标题
					'subTitle' => '更多',//右边副标题
					'right' => 0,//是否显示右边的内容
					'arrow' => 1,//是否显示右边箭头
					'url' => 'index/index',
				],
			],
		];
		$temp['json'] = json_encode($temp['array']);
		return $temp;
    }
}