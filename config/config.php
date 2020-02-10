<?php
defined('WHO_YOU_ARE') || die('access deny!');

/*
*配置文件
*/
return [
	//数据库操作
	'mysql'=>[
		'db'=>'mvc',
		'host'=>'127.0.0.1',
		'user'=>'root',
		'pwd'=>'root',		
	],

    'upload_pic_dir'=>ROOT . 'upload/upload_pic/',
    'thumb_pic_dir'=>ROOT . 'upload/thumb_pic/',
	
	'redis'=>[
		'host'=>'127.0.0.1',
		'port'=>6379,
	],
	

];











