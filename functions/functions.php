<?php
defined('WHO_YOU_ARE') || die('access deny!');
/*
*常用函数文件
*/



/*
 *目录创建函数
 *@param $path  仅支持绝对路径
 *@param $mode 默认仅u支持读写执行,g+o读写
 */

function _mkdir($path,$mode = 0766)
{
    if(strpos($path,'.') || strpos($path,'..')) return false;


    //判断目录是否存在
    if(is_dir($path)) return true;

    $res = @mkdir($path,$mode,true);
    if(!$res) return false;


    return true;
}

/*
*模板引入
*/
function view($path)
{
	return @include_once(ROOT . VIEW . '/' . $path);
}

/*
*json数据返回
*/
function json($str)
{
	header('Content-Type:application/json; charset=utf-8');
	if(!is_scalar($str) && !json_encode($str)) exit('json data error');
	
	//注意中文编码
	exit(json_encode(['code'=>200,'data'=>$str],JSON_UNESCAPED_UNICODE));
} 



