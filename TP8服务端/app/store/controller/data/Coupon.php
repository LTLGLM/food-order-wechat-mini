<?php
namespace app\store\controller\data;

use app\store\controller\Controller;
use app\store\model\Coupon as CouponModel;
use think\facade\View;

/**
 * 优惠券控制器
 */
class Coupon extends Controller
{ 
	//列表
	public function lists(){
		$model = new CouponModel;
        $list = $model->getList();
		for($n=0;$n<sizeof($list);$n++){
			$list[$n]['image'] = base_url() . 'assets/img/coupon.jpg';
			$list[$n]['params'] = json_encode($list[$n]);
		}
		View::layout(false);//不适用布局模板
		return View::fetch('lists', compact('list'));
	}
	
}
