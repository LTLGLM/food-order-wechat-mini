<?php
namespace app\api\model\exchange;

use app\common\model\exchange\Goods as GoodsModel;

/**
 * 商品模型
 */
class Goods extends GoodsModel
{
    /**
     * 获取商品列表
     */
    public function indexList(int $goods_type = 0,$limit = 0)
    {
        // 筛选条件
        $filter = [
            'is_delete' => 0    
        ];
        $goods_type > 0 && $filter['goods_type'] = $goods_type;
        // 排序规则
        $sort = ['goods_sort', 'goods_id' => 'desc'];
        
        // 执行查询
        $model = $this->with(['image','coupon'])
            ->where($filter)
            ->order($sort);
        if($limit > 0){
           $model->limit($limit);
        }
        return $model->select();
    }
}
