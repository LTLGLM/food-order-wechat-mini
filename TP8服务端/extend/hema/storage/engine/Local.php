<?php
namespace hema\storage\engine;

/**
 * 本地文件驱动
 */
class Local extends Basics
{
    /**
     * 上传图片文件
     * @return array|bool
     */
    public function upload()
    {
        // 验证文件类型
        $saveFileInfo = $this->getSaveFileInfo();
        $value = get_addons_config('upload');
        if($value['file_ext']){
            $file_ext = explode(',',$value['file_ext']);
        }else{
            $file_ext = ['jpg','jpeg','png','bmp','gif'];
        }
        // 文件扩展名
        if(!in_array($saveFileInfo['file_ext'], $file_ext)){
            $this->error = '上传图片类型只能为：' . $value['file_ext'] .'格式';
            return false;
        }
        if($value['file_size']){
            $file_size = (int)$value['file_size'];
        }else{
            $file_size = 2;//默认值
        }
        // 文件大小(字节)不能大于2M（1024*1024 = 1048576）
        if($saveFileInfo['file_size'] > ($file_size * 1048576)){
            $this->error = '上传图片大于' . (string)$file_size . 'M';
            return false;
        }
        try {
            $fileInfo = pathinfo($this->file);
            $savePath = root_path() . 'public/uploads/'.$saveFileInfo['folder'];
            $info = $this->file->move($savePath);
            //修改文件名称
            rename($savePath . DIRECTORY_SEPARATOR . $fileInfo['basename'],$savePath . DIRECTORY_SEPARATOR . $saveFileInfo['hash_name']);
            return $saveFileInfo;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件
     * @param string $filePath
     * @return bool|mixed
     */
    public function delete(string $filePath)
    {
        // 文件所在目录
        $realPath = realpath(web_path() . "uploads/{$filePath}");
        return $realPath === false || unlink($realPath);
    }

}