<?php
namespace app\common\model;

/**
 * 公众号关键字回复模型
 */
class Keyword extends BaseModel
{
    // 定义表名
    protected $name = 'keyword';

    // 定义主键
    protected $pk = 'keyword_id';

    // 追加字段
    protected $append = [];
    //是否开启全局查询	
	protected $isGlobalScopeShopId = false;

    /**
     * 消息类型
     */
    public function getTypeAttr($value)
    {
        $status = [
            'news' => '图文', 
            'text' => '文本', 
            'image' => '图片', 
            'video' => '视频', 
            'voice' => '语音', 
            'music' => '音乐', 
            'wxcard' => '卡券'
        ];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 消息类型
     */
    public function getIsOpenAttr($value)
    {
        $status = ['关闭','开启'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 获取器: 转义数组格式
     */
    public function getContentAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 修改器: 转义成json格式
     */
    public function setContentAttr($value)
    {
        return json_encode($value);
    }
    
    /**
     * 获取列表
     */
    public function getList()
    {
        // 筛选条件
        $filter = [];
        // 执行查询
        return $this->where($filter)
            ->order('keyword_id','desc')
            ->paginate(['list_rows'=>15,'query' => request()->param()]);
    }
    
    /**
     * 添加
     */
    public function add(array $data)
    {
        if($data['type'] != 'text'){
            if(!$material = Material::mediaId($data['content']['media_id'])){
                $this->error = '素材不存在';
                return false; 
            }
            //视频消息
            if($data['type'] == 'video'){
                //获取视频素材内容
                $data['content']['title'] = $material['name'];
                $data['content']['description'] = $material['introduction'];
            }
            //图文消息
            if($data['type'] == 'news'){
                $data['content']['article_count'] = sizeof($material['text']);
                $item = [];
                foreach ($material['text'] as $vo){
                    $item[] = [
                        'title' => $vo['title'],
                        'description' => $vo['digest'],
                        'pic_url' => $vo['url'],
                        'url' => $vo['content_source_url'],
                    ];
                }
                $data['content']['articles'] = $item;
            }
        }
        return $this->save($data);
    }

    /**
     * 编辑
     */
    public function edit($data)
    {
        if($data['type'] != 'text'){
            if(!$material = Material::mediaId($data['content']['media_id'])){
                $this->error = '素材不存在';
                return false; 
            }
            //视频消息
            if($data['type'] == 'video'){
                //获取视频素材内容
                $data['content']['title'] = $material['name'];
                $data['content']['description'] = $material['introduction'];
            }
            //图文消息
            if($data['type'] == 'news'){
                $data['content']['article_count'] = sizeof($material['text']);
                $item = [];
                foreach ($material['text'] as $vo){
                    $item[] = [
                        'title' => $vo['title'],
                        'description' => $vo['digest'],
                        'pic_url' => $vo['url'],
                        'url' => $vo['content_source_url'],
                    ];
                }
                $data['content']['articles'] = $item;
            }
        }
        return $this->save($data) !== false;
    }

    /**
     * 删除
     */
    public function remove()
    {
        return $this->delete();
    }
    
    /**
     * 更新状态
     */
    public function status()
    {
        $this->is_open['value'] == 1 ? $this->is_open=0 : $this->is_open=1;
        return $this->save();
    }

    /**
     * 根据关键字查询
    */
    public static function getKeys(string $key = '')
    {
        // 筛选条件
        $filter = [
            'is_open' => 1,
            'keyword' => $key,
        ];
        return self::where($filter)->find();
    }
}
