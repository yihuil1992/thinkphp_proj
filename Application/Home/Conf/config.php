<?php
return array(
	//'配置项'=>'配置值'
	
	//数据库配置
	'DB_TYPE'=>'mysql',
	'DB_USER'=>'root',
	'DB_PWD' =>'11111111',
	'DB_HOST'=>'127.0.0.1',
	'DB_PORT'=>3306,
	'DB_NAME'=>'test',
	'DB_CHARSET'=>'utf8',
	// 'DB_DSN'=>'mysql://root:root@localhost:3306/test',

	// 显示页面Trace信息
	'SHOW_PAGE_TRACE' =>true, 

	'TMPL_PARSE_STRING'  =>array(
     	'__PUBLIC__' => '/Application/Home/Public', // 更改默认的/Public 替换规则
	)
);