<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');
class Upload
{
    private static $ins;
    private $ext;
    private $size;
    private $path;
    private $upload = [];  //最终导出路径

    const ZERO = 0;
    const ONE = 1;
    const FOUR = 4;

    private $error = [
        true,
        '文件超过ini设置最大值',
        '文件大小超过表单MAX_FILE_SIZE指定值',
        '只有部分文件被上传',
        '没有文件被上传',
        false,
        '找不到临时文件夹',
        '文件写入失败',
    ];

    /*
     *@param $ext 允许的文件后缀
     *@param $size 允许的文件大小,默认为2M
     *@param $path 路径
     */

    final private function __construct($path,$ext,$size)
    {
        $this->ext  = $ext;
        $this->size = $size;
        $this->path = $path; 
		
		$this->mkdir();
    }

    final private function __clone(){}



    public static function getIns($path,$ext = ['png','jpg','jpeg'],$size = 2097152)
    {
        if(!self::$ins instanceof self) self::$ins = new self($path,$ext,$size);
        
        
        return self::$ins;
    }


    /*
     *文件上传
     *
     */
    public function upload()
    {
        if(empty($_FILES)) return false;
		
		
		//过滤
        foreach($_FILES as $k=>$v)
        {
            //大小判断,为0舍弃
            if(is_scalar($v['size']) && $v['size'] == self::ZERO && $v['error'] = self::FOUR)
            {
                unset($_FILES[$k]);
                continue;
            }   
            
            if(is_array($v['size']))
            {
                foreach($v['size'] as $kk=>$vv)
                {
                    if($vv == self::ZERO && $v['error'][$kk] == self::FOUR)  unset($_FILES[$k]['name'][$kk],$_FILES[$k]['type'][$kk],$_FILES[$k]['tmp_name'][$kk],$_FILES[$k]['error'][$kk],$_FILES[$k]['size'][$kk]);
                }
            }
        }
		
		
		
        foreach($_FILES as $k=>$v)
        {
            //出错检测
            if(is_array($v['error'])) $v['error'] = max($v['error']);
            if($v['error'] != self::ZERO) return $this->error[$v['error']];
            //后缀检测,太麻烦,直接检测name,想起了来改成type
            if(is_scalar($v['name']))
            {
                if(!in_array(substr(strrchr($v['name'],'.'),1),$this->ext))  return '仅支持' . implode('|',$this->ext) . '格式';
            }                   
            if(is_array($v['name']))
            {
                foreach($v['name'] as $vv)
                {
                    if(!in_array(substr(strrchr($vv,'.'),1),$this->ext))  return '仅支持' . implode('|',$this->ext) . '格式';
                }
            }

            
            if(is_array($v['tmp_name']))
            {
                foreach($v['tmp_name'] as $kk=>$vv)
                {
					if(!is_uploaded_file($vv)) return false;
					
					
                    $res = $this->fileUpload($_FILES[$k]['name'][$kk],$vv);
                    if(!$res) return false;
                }
            }else
			{
				$res = $this->fileUpload($v['name'],$v['tmp_name']);				
			}
			
            if(!$res) return false; 
        
        }   
		
		if(count($this->upload) == self::ONE) return $this->upload[0];
		
		
        return $this->upload;
             

    }





    /*
     *文件上传
     *@param $name 上传文件名
     *@param $tmp_name 临时文件地址
     */
    public function fileUpload($name,$tmp_name)
    {
         $new_name = $this->path . $this->rename() . $this->getExt($name);
	
         $res = move_uploaded_file($tmp_name,$new_name);
         if(!$res) return false;
           
         $this->upload[] = $new_name;
			
         return true;
    }
    


    /*
     *创建目录
     *
     */
    public function mkdir()
    {
        $res =  _mkdir($this->path);
        if(!$res) return false;

        return true;
    }

    
    /*
     *重命名
     *
     */
    public function rename()
    {
        $time = time() . str_shuffle(time()) . rand(1111,9999);
        return $time;
    }

    /*
     *获取文件后缀
     *
     */
    public function getExt($name)
    {
        return strrchr($name,'.');
    }






}




