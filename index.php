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
require(ROOT . 'functions/functions.php');
require(ROOT . 'class/Config.class.php');
require(ROOT . 'class/Log.class.php');
require(ROOT . 'class/Mysql.class.php');
require(ROOT . 'class/Upload.class.php');

$x = (Config::getIns())->upload_pic_dir;
$a =    Upload::getIns($x);
$b = $a->upload();
var_dump($b);





