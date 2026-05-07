<?php
namespace app\api\controller;

use app\api\model\Goods as GoodsModel;
use app\api\model\Page;
use app\api\model\Setting;

/**
 * 首页控制器
 */
class Index extends Controller
{
    /**
     * 首页diy数据
     */
    public function page($district='')
    {
        // 页面元素
        $appletPage = Page::getHome();
        $items = $appletPage['page_data']['array']['items'];
		for($n=0;$n<sizeof($items);$n++){
			//公告组
			if($items[$n]['type']=='notice'){
			    if($items[$n]['params']['direction']=='row'){
			        $items[$n]['data'] = [
    					'text' => $items[$n]['data'][0]['text'],
    					'url' => $items[$n]['data'][0]['url']
    				];  
			    }else{
    				$text = [];
    				$url = [];
    				for ($i=0; $i<sizeof($items[$n]['data']); $i++) {
    					$text[] = $items[$n]['data'][$i]['text'];
    					$url[] = $items[$n]['data'][$i]['url'];
    				}
    				$items[$n]['data'] = [
    					'text' => $text,
    					'url' => $url
    				];
			    }
			}
			//点单组
			if($items[$n]['type']=='order'){
			    $order_tab = Setting::getItem('order')['order_mode']['tab'];
			    $tab = $items[$n]['data'];
			    $arr = [];
			    //自动筛选点单项
			    foreach ($order_tab as $vo){
			        foreach ($tab as $vo2){
			            if($vo['value'] == $vo2['order_mode']){
			                $arr[] = $vo2;
			                break;
			            }
			        }
			    }
			    //判断有无点单项
			    if(sizeof($arr) > 0){
			        $items[$n]['params']['number'] = sizeof($arr);//计算实际开启了几个选项
			        $items[$n]['data'] = $arr;
			    }else{
			        unset($items[$n]);
			    }
			}
			//商品组
			if($items[$n]['type']=='goods' AND $items[$n]['params']['source']=='auto'){
				$limit = $items[$n]['params']['auto']['showNum'];
				$sortType = $items[$n]['params']['auto']['goodsSort'];
				$list = GoodsModel::getAll($sortType,$limit);
				$news = [];
				for($m=0;$m<sizeof($list);$m++){
					$news[$m] = [
						'goods_id' => $list[$m]['goods_id'],
						'goods_name' => $list[$m]['goods_name'],
						'image' => $list[$m]['image']['url'],
						'goods_price' => $list[$m]['spec'][0]['goods_price'],
						'line_price' => $list[$m]['spec'][0]['line_price'],
						'selling_point' => $list[$m]['selling_point'],
						'goods_sales' => $list[$m]['goods_sales']
					];
				}
				$items[$n]['data'] = $news;
			}
		}
        return $this->renderSuccess($items);
    }
}