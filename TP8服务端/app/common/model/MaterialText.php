<?php
namespace app\common\model;

/**
 * 微信公众号图文素材模型
 */
class MaterialText extends BaseModel
{
    // 定义表名
    protected $name = 'material_text';
    
    // 定义主键
    protected $pk = 'material_text_id';
    
    // 追加字段
    protected $append = ['file_url'];
    
    //是否开启全局查询	
	protected $isGlobalScopeShopId = false;
    
    /**
     * 获取图片完整路径
    */ 
    public function getFileUrlAttr($value,$data)
    {
        return uploads_url() . '/' . $data['file_path'];
    }
    
    /**
     * 获取列表
    */
    public function getList(string $text_no,string $name = ''){
        $list = $this->where(['text_no' => $text_no])->order('id','asc')->select();
        $list[0]['name'] = $name;
        return json_encode($list);
    } 
    
    /**
     * 添加
     */
    public function add(array $data, string $text_no)
    {
        //重组数据
        for($n=0;$n<sizeof($data);$n++){
            $data[$n]['id'] = $n;
            $data[$n]['text_no'] = $text_no;
        }
        return $this->saveAll($data);
    }
    
    /**
     * 添加
     */
    public function edit(array $data)
    {
        return $this->saveAll($data) !== false;
    }
    
}