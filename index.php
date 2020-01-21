<?php


/*
 *入口文件
 *
 */


//根目录
define('ROOT',str_replace('\\','/',__DIR__) . '/');



//设置报错级别
define('DEBUG',true);

if(defined('DEBUG'))
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(0);
}


require(ROOT . 'config/config.php');
require(ROOT . 'class/config.class.php');
require(ROOT . 'class/log.class.php');
require(ROOT . 'class/mysql.class.php');


