<?php
namespace app\store\controller;

use app\store\model\Comment as CommentModel;
use app\store\model\User as UserModel;
use app\store\model\Order as OrderModel;
use app\store\model\Goods as GoodsModel;
use app\store\model\Pact as PactModel;
use app\store\model\RechargeLog as RechargeLogModel;
use think\facade\View;

/**
 * 后台首页
 */
class Index extends Controller
{
    
    public function index()
    {
    	$count = [
    	    'score' => CommentModel::score(),
    	    'user' => UserModel::getCount(),
    	    'order' => OrderModel::getCount(),
    	    'goods' => GoodsModel::getCount(),
    	    'pact' => PactModel::getCount(),
    	    'recharge' => RechargeLogModel::getCount(),
    	];
    	return View::fetch('index', compact('count'));
    }
}
