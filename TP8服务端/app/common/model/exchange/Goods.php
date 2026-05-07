<?php
namespace app\common\model\exchange;

use app\common\model\BaseModel;

/**
 * 商品模型
 */
class Goods extends BaseModel
{
    // 定义表名
    protected $name = 'exchange_goods';
    // 定义主键
    protected $pk = 'goods_id';
    
    protected $append = [];

    /**
     * 关联商品图片表
     */
    public function image()
    {
        return $this->belongsTo('addons\\upload\\model\\UploadFile','image_id');
    }
    
    /**
     * 关联优惠券表
     */
    public function coupon()
    {
        return $this->belongsTo('app\\common\\model\\Coupon','coupon_id');
    }
    
    /**
     * 商品类型
     */
    public function getGoodsTypeAttr($value)
    {
        $status = [10 => '实物', 20 => '优惠券'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 显示状态
     */
    public function getGoodsStatusAttr($value)
    {
        $status = [10 => '上架', 20 => '下架'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 是否推荐
     */
    public function getIsRecommendAttr($value)
    {
        $status = ['否', '是'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取商品列表
     */
    public function getList(int $goods_type = 0,string $search = '')
    {
        // 筛选条件
        $filter = [
            'is_delete' => 0    
        ];
        $goods_type > 0 && $filter['goods_type'] = $goods_type;
        !empty($search) && $filter['goods_name'] = ['like', '%' . trim($search) . '%'];
        // 排序规则
        $sort = ['goods_sort', 'goods_id' => 'desc'];
        
        // 执行查询
        return $this->with(['image','coupon'])
            ->where($filter)
            ->order($sort)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    
    /**
     * 详情
     */
    public static function detail($id)
    {
        return self::with(['image','coupon'])
            ->where(['goods_id' => $id])
            ->find();
    }
    
    /**
     * 添加
     */
    public function add(array $data)
    {
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        if (!isset($data['image_id']) || empty($data['image_id'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        return $this->save($data);	
    }
    
    /**
     * 编辑
     */
    public function edit(array $data)
    {
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        if (!isset($data['image_id']) || empty($data['image_id'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        return $this->save($data) !== false;
    }

    /**
     * 删除
     */
    public function remove()
    {
        //软删除
        return $this->save([
            'is_delete' => 1    
        ]);
    }
    /**
     * 设置商品上下架
     */
    public function status()
    {
        $this->goods_status['value'] == 10 ? $this->goods_status = 20 :$this->goods_status = 10;
        return $this->save() !== false;
    }
}