<?php

namespace hema;

/**
 * Zip 文件包工具类
 */
class ZipFolder
{
    private $root;
    private $password;
    private $ignored_names = [];
    private $error;

    /**
     * 解压
     * $zipfile 将要生成的压缩文件路径及压缩包名称
     * $folder 将要被压缩的文件夹路径
     * $password 压缩密码
     * @return 成功返回true 否则返回false
     */
    public function unzip($zipfile,$folder,$password='')
    {
        $zip = new \ZipArchive();
        try {
            $zip->open($zipfile);
        } catch (Exception $e) {
            $zip->close();
            @unlink($zipfile);// 移除临时文件
            $this->error = '压缩包打开失败';
            return false;
        }
        try {
            if (!is_dir($folder)) {
                @mkdir($folder, 0777);
            }
            $zip->extractTo($folder);
        } catch (Exception $e) {
            @rmdirs($folder);//删除目录
            $this->error = '解压失败';
            return false;
        } finally {
            @unlink($zipfile);// 移除临时文件
            $zip->close();
        }
        return true;
    }
    
    /**
     * 创建压缩文件
     * $zipfile 将要生成的压缩文件路径及压缩包名称
     * $folder 将要被压缩的文件夹路径
     * $password 压缩密码
     * $ignored 要忽略的文件列表
     * @return 成功返回true 否则返回false
     */
    public function zip($zipfile,$folder,$password='',$ignored = [])
    {
        $this->password = $password;
        $this->root = $folder;
        array_push($ignored,'upgrade');//添加数组元素
        $this->ignored_names = $ignored;
        $zip = new \ZipArchive();
        //打开一个压缩文件，如果没则新建一个,如果存在则覆盖
        if($zip->open($zipfile, \ZipArchive::CREATE) !== true){
            $this->error = '临时压缩包文件创建失败';
            return false;
        }
        $this->createZip($zip);
        if($zip->close()){
            return true;
        }
        $this->error = '创建压缩包失败';
        return false;
    }

    /**
     * 递归添加文件到压缩包
     * $son_dir 添加到压缩包的子目录
     * return void
     */
    private function createZip($zip,$folder = '',$parent = '')
    {
        $full_path = $this->root . $parent . $folder;
        $zip_path = $parent . $folder;
        if(empty($zip_path)){
            $save_path = '';//压缩包内存放目录
        }else{
           $zip->addEmptyDir($zip_path); 
           $save_path = $zip_path . '/';
        }
        $dir = new \DirectoryIterator($full_path);
        foreach ($dir as $file){
            if(!$file->isDot()){
                $filename = $file->getFilename();
                if(!in_array($filename,$this->ignored_names)){
                    if($file->isDir()){
                        $this->createZip($zip,$filename,$save_path);
                    }else{
                        //第二个参数是重命名文件名，带上路径就可以改变当前文件在压缩包里面的路径
                        if(!empty($this->password)){
                            if($filename !== 'local.ini'){
                                $zip->setPassword($this->password);// 设置密码 第一步
                            }
                            $zip->addFile($full_path . '/' . $filename,$save_path . $filename);
                            if($filename !== 'local.ini'){
                                $zip->setEncryptionName($save_path . $filename, \ZipArchive::EM_AES_256);// 设置密码 第二步
                            }
                        }else{
                            $zip->addFile($full_path . '/' . $filename,$save_path . $filename);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 读取压缩包文件与目录列表
     * $zipfile 压缩包文件
     * $return array 文件与目录列表
     */
    public function fileList($zipfile)
    {
        $file_dir_list = array();
        $file_list = array();
        if($this->zip->open($zipfile) === true){
            for($i=0;$i<$this->zip->numFiles;$i++){
                $numfiles = $this->zip->getNameIndex($i);
                if(preg_match('/\/$/i',$numfiles)){
                    $file_dir_list[] = $numfiles;
                }else{
                    $file_list[] = $numfiles;
                }
            }
        }
        return array('files' => $file_list,'dirs' => $file_dir_list);
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    /****** 以下为示例方法 *******/
    
    //压缩一个文件
    public function demo1()
    {
        $path = "demo.txt";
        $filename = 'demo.zip';
        $zip = new ZipArchive();
        $zip->open($filename,ZipArchive::CREATE);//打开压缩包，第一个参数是压缩后的文件路径及压缩包名（默认是当前目录下）
        $zip->addFile($path,basename($path));//向压缩包添加文件
        $zip->close();//关闭压缩包
    }
    //压缩多个文件
    public function demo2()
    {
        $fileList = array(
            'f:/phpstudy/www/log.txt',//绝对路径
            './weixin.class.php',//相对路径
        );
        $filename = 'demo.zip';
        $zip = new ZipArchive();
        $zip->open($filename,ZipArchive::CREATE);//打开压缩包，第一个参数是压缩后的文件路径及压缩包名（默认是当前目录下）
        foreach ($fileList as $file){
            $zip->addFile($file,basename($file));//向压缩包添加文件
        }
        $zip->close();//关闭压缩包
    }
    
     //压缩目录，使用上面的压缩类进行操作
    public function demo3()
    {
        require_once '引入压缩类文件路径';
        $zip = new ZipFolder();//穿件压缩类
        //第一个参数是压缩后的文件路径及包名（默认在当前目录）
        //第二个参数是需要压缩的路径
        $res = $zip->zip('./zipfolder/1.zip','./zipfolder');
    }
    
}
