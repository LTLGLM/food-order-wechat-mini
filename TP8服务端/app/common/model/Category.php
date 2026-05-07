<?php
namespace app\common\model;

/**
 * 商品分类模型
 */
class Category extends BaseModel
{
    // 定义表名
    protected $name = 'category';
    // 定义主键
    protected $pk = 'category_id';
    // 追加字段
    protected $append = [];
  
    /**
     * 获取列表
     */
    public function getList($page=true)
    {
        $filter = [
            'is_delete' => 0    
        ];
        $model = $this->where($filter)
            ->order(['sort','category_id'=>'desc']);
        if($page){
            return $model->paginate(['list_rows'=>15,'query' => request()->param()]);
        }
        // 执行查询
        return $model->select()->toArray();
    }
    
    /**
     * 添加
     */
    public function add(array $data)
    {
        return $this->save($data);
    }
  
    /**
     * 修改
     */
    public function edit(array $data)
    {
        return $this->save($data)!==false;      
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
}

    