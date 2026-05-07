<?php
namespace app\store\controller\data;

use app\store\controller\Controller;
use app\store\model\Goods as GoodsModel;
use think\facade\View;

/**
 * 商品控制器
 */
class Goods extends Controller
{ 
	//列表
	public function lists(){
		$model = new GoodsModel;
        // 筛选条件
        $filter = [
            'is_delete' => 0    
        ];
        // 排序规则
        $sort = ['goods_sort', 'goods_id' => 'desc'];
        $list = $model->with(['image','category'])
            ->where($filter)
            ->order($sort)
            ->select()->toArray();
		for($n=0;$n<sizeof($list);$n++){
			$list[$n]['image'] = $list[$n]['image']['url'];
			$list[$n]['name'] = $list[$n]['goods_name'];
			$list[$n]['params'] = json_encode($list[$n]);
		}
		View::layout(false);//不适用布局模板
		return View::fetch('lists', compact('list'));
	}
	
}
