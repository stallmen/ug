<?php


/*
 *入口文件
 *
 */



//根目录
define('ROOT',str_replace('\\','/',__DIR__) . '/');
//文件访问限制
define('WHO_YOU_ARE',true);

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
require(ROOT . 'functions/functions.php');

spl_autoload_register(function($class)
{
	include(ROOT  . str_replace('\\','/',$class) . '.class.php');
});






