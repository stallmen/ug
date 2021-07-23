<?php


/*
 *入口文件
 *
 */



if(version_compare(PHP_VERSION,'8.0.0','<'))    exit('请选择PHP8.0以上版本!');


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

defined('LIB') || define('LIB','Lib');
defined('MODEL') || define('MODEL','Model');
defined('CONTROLLER') || define('CONTROLLER','Controller');



spl_autoload_register(function($class)
{
	require_once(ROOT . 'config/config.php');
	require_once(ROOT . 'functions/functions.php');
		
	$class = str_replace('\\','/',$class);
	
	@include_once(ROOT   . $class . '.php');

});

/*
*路由实现 先用着
*模块-控制器-方法
*/
$path = $_SERVER['QUERY_STRING'];

if(!empty($path))
{
	$path = explode('/',$path);

	if(count($path) != 3)
	{
		header("Location:localhost");
	}else
	{
		$str = $path[0] . '\\' . $path[1];
		$obj = new $str;
		call_user_func([$obj,$path[2]]);
	}		
}

















