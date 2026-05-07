<?php
namespace app\common\model;

use think\facade\Db;

/**
 * 商品模型
 */
class Goods extends BaseModel
{
    // 定义表名
    protected $name = 'goods';
    // 定义主键
    protected $pk = 'goods_id';
    
    protected $append = ['stock','month_sales'];
    
    /**
     * 商品库存
     */
    public function getStockAttr($value, $data)
    {
        return (new GoodsSpec)->where('goods_id',$data['goods_id'])->sum('stock_num');
    }

    /**
     * 计算月销量
     */
    public function getMonthSalesAttr($value, $data)
    {
        $time = time()-(30*24*60*60);
        return OrderGoods::where('goods_id',$data['goods_id'])
            ->where('create_time','>',$time)
            ->sum('total_num');
    }
    /**
     * 关联商品分类表
     */
    public function category()
    {
        return $this->belongsTo('app\\common\\model\\Category','category_id');
    }
    /**
     * 关联商品规格表
     */
    public function spec()
    {
        return $this->hasMany('app\\common\\model\\GoodsSpec','goods_id')->order(['goods_spec_id' => 'asc']);
    }
    /**
     * 关联商品规格关系表
     */
    public function specRel()
    {
        return $this->belongsToMany('app\\common\\model\\SpecValue', 'app\\common\\model\\GoodsSpecRel','spec_value_id','goods_id');
    }
    /**
     * 关联商品图片表
     */
    public function image()
    {
        return $this->belongsTo('addons\\upload\\model\\UploadFile','image_id');
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
    public function getList(string $search = '')
    {
        // 筛选条件
        $filter = [
            'is_delete' => 0    
        ];
        !empty($search) && $filter['goods_name'] = ['like', '%' . trim($search) . '%'];
        // 排序规则
        $sort = ['goods_sort', 'goods_id' => 'desc'];

        // 商品表名称
        $tableName = $this->getTable();
        // 多规格商品 最高价与最低价
        $GoodsSpec = new GoodsSpec;
        $minPriceSql = $GoodsSpec->field(['MIN(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        $maxPriceSql = $GoodsSpec->field(['MAX(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        // 执行查询
        return $this->with(['category', 'image', 'spec', 'spec.image'])
            ->where($filter)
            ->order($sort)
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
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
        // 开启事务
        Db::startTrans();
        try {
            // 添加商品
            $this->save($data);
            // 商品规格
            $this->addGoodsSpec($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
        }
        return false;	
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
        // 开启事务
        Db::startTrans();
        try {
            // 保存商品
            $this->save($data);
            // 商品规格
            $this->addGoodsSpec($data, true);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 添加商品规格
     */
    private function addGoodsSpec(&$data, $isUpdate = false)
    {
        // 更新模式: 先删除所有规格
        $model = new GoodsSpec;
        $isUpdate && $model->removeAll($this['goods_id']);
        // 添加规格数据
        if ($data['spec_type'] == '10') {
            // 单规格
            $this->spec()->save($data['spec']);
        } else if ($data['spec_type'] == '20') {
            // 添加商品与规格关系记录
            $model->addGoodsSpecRel($this['goods_id'], $data['spec_many']['spec_attr']);
            // 添加商品sku
            $model->addSkuList($this['goods_id'], $data['spec_many']['spec_list']);
        }
    }
    
    /**
     * 根据商品id集获取商品列表 (购物车列表用)
     */
    public function getListByIds($goodsIds) {
        return $this->with(['category','image', 'spec','spec.image','spec_rel.spec'])
            ->where('goods_id', 'in', $goodsIds)->select();
    }
    
    /**
     * 获取商品详情
     */
    public static function detail($id)
    {
        return self::with(['category', 'image', 'spec', 'spec.image', 'spec_rel.spec'])->find($id);
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
    
    /**
     * 获取规格信息
     */
    public function getManySpecData($spec_rel, $spec)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($spec_rel as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['spec_id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['spec_value_id'],
                'spec_value' => $item['spec_value'],
            ];
        }
        // spec_list
        $specListData = [];
        foreach ($spec as $item) {
            $image = (isset($item['image']) && !empty($item['image'])) ? $item['image'] : ['file_id' => 0, 'url' => ''];
            $spec_name = '';
            $spec_id_list = explode('_',$item['spec_sku_id']);
            for($n=0;$n<sizeof($spec_id_list);$n++) {
                foreach ($spec_rel as $vo) {
                    if($spec_id_list[$n] == $vo['spec_value_id']){
                        $spec_name .= $vo['spec_value'];
                        if($n+1 != sizeof($spec_id_list)){
                            $spec_name .= '/';
                        }
                    }
                }
            }
            $specListData[] = [
                'goods_spec_id' => $item['goods_spec_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'spec_name' => $spec_name,
                'rows' => [],
                'form' => [
                    'image_id' => $image['file_id'],
                    'image_url' => $image['url'],
                    'goods_price' => $item['goods_price'],
                    'line_price' => $item['line_price'],
                    'stock_num' => $item['stock_num']
                ],
            ];
        }
        return ['spec_attr' => array_values($specAttrData), 'spec_list' => $specListData];
    }
    
    /**
     * 商品多规格信息
     */
    public function getGoodsSku(string $goods_sku_id = '')
    {
        $goodsSkuData = array_column($this['spec']->toArray(), null, 'spec_sku_id');
        if (!isset($goodsSkuData[$goods_sku_id])) {
            return false;
        }
        $goods_sku = $goodsSkuData[$goods_sku_id];
        // 多规格文字内容
        $goods_sku['goods_attr'] = '';
        if ($this['spec_type'] == 20) {
            $attrs = explode('_', $goods_sku['spec_sku_id']);
            $spec_rel = array_column($this['spec_rel']->toArray(), null, 'spec_value_id');
            foreach ($attrs as $specValueId) {
                if(!empty($goods_sku['goods_attr'])){
                    $goods_sku['goods_attr'] .= '/';
                }
                /*$goods_sku['goods_attr'] .= $spec_rel[$specValueId]['spec']['spec_name'] . ':'
                    . $spec_rel[$specValueId]['spec_value'];*/
                $goods_sku['goods_attr'] .= $spec_rel[$specValueId]['spec_value'];
            }
        }
        return $goods_sku;
    }
    
    /**
     * 更新商品库存销量
     * $goodsList=商品列表 $stock=是否启用库存 $inc= true（新增订单）false(退单)
     */
    public function updateStockSales($goodsList,$stock = false,$inc= true)
    {
        // 整理批量更新商品销量
        $goodsSave = [];
        // 批量更新商品规格：sku销量、库存
        $goodsSpecSave = [];
        foreach ($goodsList as $goods) {
            if($inc){
                //判断商品是否存在
                if($model = (new Goods)->get($goods['goods_id'])){
                    //增加销量
                    $model->sales_actual = ['inc', $goods['total_num']];
                    $model->save();
                    $model = (new GoodsSpec)->get($goods['goods_spec_id']);
                    $model->goods_sales = ['inc', $goods['total_num']];
                    // 判断是否减库存
                    if ($stock) {
                        if($model->stock_num > $goods['total_num']){
                            $model->stock_num = ['dec', $goods['total_num']];
                        }else{
                            $model->stock_num = 0;
                        }
                    }
                    $model->save();
                }
            }else{
                //退销量
                if($goods['refund_num'] > 0){
                    if($model = (new Goods)->get($goods['goods_id'])){
                        if($model->sales_actual > $goods['refund_num']){
                            $model->sales_actual = ['dec', $goods['refund_num']];
                        }else{
                            $model->sales_actual = 0;
                        }
                        $model->save();
                        if($model = (new GoodsSpec)->get($goods['goods_spec_id'])){
                            if($model->goods_sales > $goods['refund_num']){
                                $model->goods_sales = ['dec', $goods['refund_num']];
                            }else{
                                $model->goods_sales = 0;
                            }
                            // 判断是否减库存
                            if ($stock) {
                                $model->stock_num = ['inc', $goods['refund_num']];
                            }
                            $model->save();
                        }
                    }
                }
            }
        }
        return true;
    }
    
    /**
     * 获取商品总数
     */
    public static function getCount()
    {
        return self::count();
    }

}