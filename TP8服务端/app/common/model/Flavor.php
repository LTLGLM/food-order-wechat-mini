<?php
namespace app\common\model;

/**
 * 口味选项模型
 */
class Flavor extends BaseModel
{
    // 定义表名
    protected $name = 'flavor';
    // 定义主键
    protected $pk = 'flavor_id';
    // 追加字段
    protected $append = [];
  
    /**
     * 获取列表
     */
    public function getList($page=true)
    {
        $model = $this->order(['flavor_id'=>'desc','sort'=>'asc']);
        if($page){
            return $model->paginate(['list_rows'=>15,'query' => request()->param()]);
        }
        // 执行查询
        return $model->select();
    }
    
    /**
     * 添加
     */
    public function add(array $data)
    {
        return $this->save($data);
    }
  
    /**
     * 编辑
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
        return $this->delete();
    }
}

    