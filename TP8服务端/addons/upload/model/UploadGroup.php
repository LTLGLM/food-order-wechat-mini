<?php
namespace addons\upload\model;

use think\Model;

/**
 * 文件库分组模型
 */
class UploadGroup extends Model
{
    // 定义表名
    protected $name = 'upload_group';

    // 定义主键
    protected $pk = 'group_id';

    /**
     * 分组详情
     */
    public static function get($group_id) 
    {
        return self::where('group_id',$group_id)->find();
    }
    
    /**
     * 获取列表记录
     */
    public function getList($applet_id = 0,$shop_id = 0,$user_id = 0, string $group_type = 'image')
    {
        $filter = [
            'group_type' => $group_type,
            'applet_id' => $applet_id,
            'user_id' => $user_id,
            'shop_id' => $shop_id
        ];
        return $this->where($filter)->order(['sort' => 'asc'])->select();
    }

    /**
     * 添加新记录
     */
    public function add(array $data)
    {
        $data['sort'] = 100;
        return $this->save($data);
    }

    /**
     * 更新记录
     */
    public function edit(array $data)
    {
        return $this->save($data) !== false;
    }

    /**
     * 删除记录
     */
    public function remove($applet_id = 0,$shop_id = 0,$user_id = 0)
    {
        $filter = [
            'applet_id' => $applet_id,
            'user_id' => $user_id,
            'shop_id' => $shop_id,
            'group_id' => $this->group_id
        ];
        // 更新该分组下的所有文件
        $model = new UploadFile;
        $model->where($filter)->update(['group_id' => 0]);
        return $this->delete();
    }

}
