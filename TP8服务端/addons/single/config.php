<?php
/** 
 * 配置文件 
 */
return [ 
	[
	    'name' => 'app_name',//参数名称
	    'title' => '小程序名称：', //显示标题
	    'type' => 'text', //表单元素类型 - 文本框
	    //属性
	    'attribute' => [ 
	        'placeholder' => '请输入小程序页面显示的名称',
	        'maxlength' => '150',//可输入最大长度
	        'required' => true,//是否必填
	        'disabled' => false,//是否禁用
	    ],
	    'msg' => '',//提示内容
	    'value' => '河马点单',//初始值
	],
	[
	    'name' => 'copyright',//参数名称
	    'title' => '底部版权：', //显示标题
	    'type' => 'text', //表单元素类型 - 文本框
	    //属性
	    'attribute' => [ 
	        'placeholder' => '请输入小程序底部显示的版权信息',
	        'maxlength' => '150',//可输入最大长度
	        'required' => true,//是否必填
	        'disabled' => false,//是否禁用
	    ],
	    'msg' => '',//提示内容
	    'value' => '©河马点单',//初始值
	],
];