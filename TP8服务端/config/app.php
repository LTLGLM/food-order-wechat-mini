<?php

//应用设置
return [
    'app_host'                  => env('app.host', ''),// 应用地址
    'app_namespace'             => '',// 应用的命名空间
    'with_route'                => true,// 是否启用路由
    'default_app'               => 'store',// 默认应用
    'default_timezone'          => 'Asia/Shanghai',// 默认时区
    // 应用映射（自动多应用模式有效）
    'app_map' => [
        'store'                 => 'store',//更改管理模块名称
		'api'                 	=> 'api',
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => [
        'common'
    ],
    // 异常页面的模板文件
    'exception_tmpl'            => app()->getThinkPath() . 'tpl/think_exception.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'             => '页面错误！请稍后再试～',
    'show_error_msg'            => true,// 显示错误信息
    'hemaphp' => [
        'remote_enabled'        => false,
        'system'                => 'single',//系统类型
        'version'               => '1.0.5',//版本号
        'api_url'               => 'https://www.hemaphp.com',//API接口地址
        'cors_request_domain'   => 'localhost,127.0.0.1',//允许跨域的域名,多个以,分隔
        'unknownsources'        => false,//是否允许未知来源的插件压缩包
        'backup_global_files'   => true,//插件启用禁用时是否备份对应的全局文件
        'addon_pure_mode'       => true,//插件纯净模式，插件启用后是否删除插件目录的app、public和assets文件夹
    ],
];
