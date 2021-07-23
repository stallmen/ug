<?php
defined('WHO_YOU_ARE') || die('access deny!');

/*
*配置文件
*/
return [
	//数据库操作
	'mysql'=>[
		'dbname'=>'test',
		'host'=>'127.0.0.1',
		'user'=>'root',
		'pwd'=>'root',
        'charset'=>'utf8mb4'
	],

    'upload_pic_dir'=>ROOT . 'upload/upload_pic/',
    'thumb_pic_dir'=>ROOT . 'upload/thumb_pic/',
	
	'redis'=>[
		'host'=>'127.0.0.1',
		'port'=>6379,
	],
	'mail'=>[
		'server'=>'smtp.163.com',
		'port'=>25,
		'user'=>'woganwuhao@163.com',
		'pwd'=>'19931070Qq'
	],
	

];











