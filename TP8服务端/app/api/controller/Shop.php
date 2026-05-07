<?php
namespace app\api\controller;

use app\api\model\Setting;
use app\api\model\Comment;

/**
 * 店铺控制器
 */
class Shop extends Controller
{
	/**
     * 详情
     */
    public function detail($location='')
    {
		$shop = Setting::getItem('shop');
		$goods = Setting::getItem('goods');
		$order = Setting::getItem('order');
		$score = Comment::score();
		$shop['goods'] = $goods;
		$shop['order'] = $order;
		$shop['score'] = $score;
		if(!empty($shop['coordinate']) and !empty($location)){
		    $shop['location'] = get_distance($shop['coordinate'],$location);
		}
		return $this->renderSuccess($shop);
    }
}
