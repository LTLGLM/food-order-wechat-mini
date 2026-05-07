<?php
namespace addons\upload\model;

use think\Model;

/**
 * 文件库模型
 */
class UploadFile extends Model
{
    // 定义表名
    protected $name = 'upload_file';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 定义主键
    protected $pk = 'file_id';

    // 追加的字段
    protected $append = ['url'];

    /**
     * 获取图片完整路径
     */
    public function getUrlAttr($value, $data)
    {
        // 存储方式本地：拼接当前域名
        if ($data['storage'] === 'local') {
            return uploads_url() . $data['file_path'];
        }
        return "{$data['domain']}/{$data['file_path']}";
    }

    /**
     * 根据文件名查询文件id
     */
    public static function getFildIdByName(string $file_path)
    {
        return (new static)->where(['file_path' => $file_path])->value('file_id');
    }

    /**
     * 查询文件id
     */
    public static function getFileName($fileId)
    {
        return (new static)->where(['file_id' => $fileId])->value('file_path');
    }

    /**
     * 获取列表记录
     */
    public function getList($applet_id=0,$shop_id = 0,$user_id = 0,$group_id = 0, string $file_type = 'image')
    {
        $filter = [
            'file_type' => $file_type,
            'is_delete' => 0,
            'applet_id' => $applet_id,
            'user_id' => $user_id,
            'shop_id' => $shop_id
        ];
        $group_id > 0 && $filter['group_id'] = $group_id;//已分组条件
        $group_id < 0 && $filter['group_id'] = 0; //未分组条件
        return $this->where($filter)->order(['file_id' => 'desc'])->paginate(32);
    }


    /**
     * 文件详情
     */
    public static function get($fileId)
    {
        return self::where('file_id',$fileId)->find();
    }

    /**
     * 添加新记录
     */
    public function add(array $data)
    {
        return $this->save($data);
    }

    /**
     * 批量软删除
     */
    public function softDelete(array $fileIds)
    {
        return $this->where('file_id','in',$fileIds)->update(['is_delete' => 1]);
    }

    /**
     * 批量移动文件分组
     */
    public function moveGroup($group_id, array $fileIds)
    {   
        return $this->where('file_id','in',$fileIds)->update(compact('group_id'));
    }
    
    /**
     * 删除
     */
    public function remove($imageId)
    {
        $list = $this->where('file_id','in',$imageId)->select();
        foreach ($list as $vo){
            @unlink('./uploads/' . $vo['file_path']);
        }
        return $this->where('file_id','in',$imageId)->delete();
    }

}
