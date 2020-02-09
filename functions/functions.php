<?php

/*
 *目录创建函数
 *@param $path  仅支持绝对路径
 *@param $mode 默认仅支持读写
 */

function _mkdir($path,$mode = 0666)
{
    if(strpos($path,'.') || strpos($path,'..')) return false;


    //判断目录是否存在
    if(is_dir($path)) return true;

    $res = @mkdir($path,$mode,true);
    if(!$res) return false;


    return true;


}




