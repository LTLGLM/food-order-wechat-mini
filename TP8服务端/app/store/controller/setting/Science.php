<?php
namespace app\store\controller\setting;

use app\store\controller\Controller;
use think\facade\View;

/**
 * 环境监测
 */
class Science extends Controller
{
	/**
     * 状态class
     */
    private $statusClass = [
        'normal' => '',
        'warning' => 'am-active',
        'danger' => 'am-danger'
    ];

    /**
     * 环境检测
     */
    public function index()
    {
        return View::fetch('index', [
            'statusClass' => $this->statusClass,
            'phpinfo' => $this->phpinfo(),  // 服务器信息
            'server' => $this->server(), // PHP环境要求
            'writeable' => $this->writeable(), // 目录权限监测
        ]);
    }

    /**
     * 服务器信息
     */
    private function server()
    {
        return [
            'system' => [
                'name' => '服务器操作系统',
                'value' => PHP_OS,
                'status' => PHP_SHLIB_SUFFIX === 'dll' ? 'warning' : 'normal',
                'remark' => '建议使用 Linux 系统以提升程序性能'
            ],
            'webserver' => [
                'name' => 'Web服务器环境',
                'value' => $this->request->server('SERVER_SOFTWARE'),
                'status' => PHP_SAPI === 'isapi' ? 'warning' : 'normal',
                'remark' => '建议使用 Apache 或 Nginx 以提升程序性能'
            ],
            'php' => [
                'name' => 'PHP版本',
                'value' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '5.4.0') === -1 ? 'danger' : 'normal',
                'remark' => 'PHP版本必须为 5.4.0 以上'
            ],
            'upload_max' => [
                'name' => '文件上传限制',
                'value' => @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow',
                'status' => 'normal',
                'remark' => ''
            ],
            'web_path' => [
                'name' => '程序运行目录',
                'value' => realpath(web_path()),
                'status' => 'normal',
                'remark' => ''
            ],
        ];
    }

    /**
     * PHP环境要求
     */
    private function phpinfo()
    {
        return [
            'php_version' => [
                'name' => 'PHP版本',
                'value' => '5.4.0及以上',
                'status' => version_compare(PHP_VERSION, '5.4.0') === -1 ? 'danger' : 'normal',
                'remark' => 'PHP版本必须为 5.4.0及以上'
            ],
            'curl' => [
                'name' => 'CURL',
                'value' => '支持',
                'status' => extension_loaded('curl') && function_exists('curl_init') ? 'normal' : 'danger',
                'remark' => '您的PHP环境不支持CURL, 系统无法正常运行'
            ],
            'openssl' => [
                'name' => 'OpenSSL',
                'value' => '支持',
                'status' => extension_loaded('openssl') ? 'normal' : 'danger',
                'remark' => '没有启用OpenSSL, 将无法访问微信平台的接口'
            ],
            'pdo' => [
                'name' => 'PDO',
                'value' => '支持',
                'status' => extension_loaded('PDO') && extension_loaded('pdo_mysql') ? 'normal' : 'danger',
                'remark' => '您的PHP环境不支持PDO, 系统无法正常运行'
            ],
            'bcmath' => [
                'name' => 'BCMath',
                'value' => '支持',
                'status' => extension_loaded('bcmath') ? 'normal' : 'danger',
                'remark' => '您的PHP环境不支持BCMath, 系统无法正常运行'
            ],
            'zip' => [
                'name' => 'ZIP扩展',
                'value' => '支持',
                'status' => extension_loaded('zip') ? 'normal' : 'danger',
                'remark' => '您的PHP环境不支持ZIP扩展, 无法正常导出Excel表格'
            ],
            'gd2' => [
                'name' => 'GD扩展',
                'value' => '支持',
                'status' => extension_loaded('gd') ? 'normal' : 'danger',
                'remark' => '您的PHP环境不支持GD扩展, 无法正常生成二维码推广海报'
            ],
        ];

    }

    /**
     * 目录权限监测
     */
    private function writeable()
    {
        $paths = [
            'addons' => realpath(str_replace('public','addons',web_path())),
            'assets' => realpath(web_path()) . '/addons',
            'uploads' => realpath(web_path()) . '/uploads',
			'temp' => realpath(web_path()) . '/temp',
        ];
        return [
            'addons' => [
                'name' => '插件部署目录',
                'value' => $paths['addons'],
                'status' => $this->checkWriteable($paths['addons']) ? 'normal' : 'danger',
                'remark' => '目录不可写，系统将无法正常部署插件'
            ],
            'assets' => [
                'name' => '插件资源目录',
                'value' => $paths['assets'],
                'status' => $this->checkWriteable($paths['assets']) ? 'normal' : 'danger',
                'remark' => '目录不可写，系统将无法正常部署插件静态资源文件'
            ],
            'uploads' => [
                'name' => '文件上传目录',
                'value' => $paths['uploads'],
                'status' => $this->checkWriteable($paths['uploads']) ? 'normal' : 'danger',
                'remark' => '目录不可写，系统将无法正常上传文件'
            ],
			'temp' => [
                'name' => '临时文件目录',
                'value' => $paths['temp'],
                'status' => $this->checkWriteable($paths['temp']) ? 'normal' : 'danger',
                'remark' => '目录不可写，系统将无法正常生成门店、餐桌等二维码'
            ],
        ];

    }

    /**
     * 检查目录是否可写
     */
    private function checkWriteable(string $path)
    {
        try {
            !is_dir($path) && mkdir($path, 0755);
            if (!is_dir($path))
                return false;
            $fileName = $path . '/_test_write.txt';
            if ($fp = fopen($fileName, 'w')) {
                return fclose($fp) && unlink($fileName);
            }
        } catch (\Exception $e) {
        }
        return false;
    }
}
