<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');
class Verify
{
	private static $ins;
	private $char = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM';
	
	//默认使用内置字体
	private $font = 5; 
	
	//文件类型标识
	const GIF = 1;
	const JPG = 2;
	const PNG = 3;
	const SWF = 4;
	const PSD = 5;
	const BMP = 6;
	const JPC = 9;
	const JP2 = 10;
	const JPX = 11;
	const JB2 = 12;
	const SWC = 13;
	const IFF = 14;
	const WBMP = 15;
	const XBM = 16;
	
	
	final private function __construct()
	{
		if(!extension_loaded('gd'))  die('please load GD extension first!');
		
		
	} 
	
	final private function __clone(){}
	
	
	
	public static function getIns()
	{
		if(!self::$ins instanceof self)	 self::$ins = new self();
		
		return self::$ins;
	}
	
	/*
	*
	*验证码
	*@param $length 验证码长度
	*@param $width 宽
	*@param $height 高
	*@param $inter 干扰雪花数
	*@return 验证码图片-png格式
	*/
	public function verifyCode($length = 4,$width = 110,$height = 40,$inter = 100)
	{
		header ('Content-Type: image/png');
		$im = @imagecreatetruecolor($width, $height);
		
		//获取随机字符串
		$char = $this->getChar($length);


		for($i = 0;$i<$length;$i++)
		{
			//随机分配颜色
			$color = $this->getColor();
				
			$text_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
			imagestring($im, $this->font, intdiv($width,$length)*$i+1, mt_rand(0,intdiv($height,2)), $char[$i], $text_color);
			
		}
		
		for($i=0;$i<$inter;$i++)
		{
			//随机分配颜色
			$color = $this->getColor();
	
			//干扰色
			$inter_color = imagecolorallocate($im,$color[0], $color[1], $color[2]);
			
			imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$inter_color);
			
		}
	
		imagepng($im);
		imagedestroy($im);
	}
	
	/*
	*获取随机颜色
	*/
	private function getColor()
	{
		$red = mt_rand(0,255);
		$green = mt_rand(0,255);
		$blue = mt_rand(0,255);
		
		return [$red,$green,$blue];
	}

	/*
	*获取随机字符串
	*/
	public function getChar($length)
	{
		return substr(str_shuffle($this->char),0,$length);
	}
	
	/*
	*缩略图及水印功能
	*@param $path 图片路径/base64数据流
	*@param $watermark 水印图片
	*@param $width 缩略图高
	*@param $height 缩略图宽
	*@param $watermark_width 水印图宽
	*@param $watermark_height 水印图高
	*@param $water_x 水印位置X坐标
	*@param $water_y 水印位置Y坐标
	*@param $gray 是否为灰色/黑白照
	*@param $pac 透明程度0-100/默认为50半透明
	*return 缩略图路径
	*/
	public function thumb($path,$watermark = '',$gray = false,$width = 40,$height = 40,$watermark_width = 10 , $watermark_height = 10,$water_x = 30,$water_y = 30,$pac = 50)
	{
		
		//目录检测及创建
		$thumb_dir = $this->checkDir();
		if(!is_dir($thumb_dir)) return $thumb_dir;
		
		$file_info = @getimagesize($path);
		if(!$file_info) return '无法读取图片';
		
		//文件资源对象
		$img = $this->read_img($path,$file_info[2]);
		if(is_null($img)) return '图片读取失败';
	
		
		$new_img = @imagecreatetruecolor($width, $height);
		imagecopyresampled($new_img,$img,0,0,0,0,$width,$height,$file_info[0],$file_info[1]);
		
		
		
		$new_name = $thumb_dir . time() . str_shuffle(time()) . rand(1111,9999) . '--' . $width . '-' . $height . '.png';
		
		//水印功能
		if(!empty($watermark))
		{
			$water_file_info = @getimagesize($watermark);
			if(!$water_file_info) return '无法读取图片';
			
			$water_img = $this->read_img($watermark,$water_file_info[2]);
			if(is_null($water_img)) return '图片读取失败';
			
			$new_water_img = @imagecreatetruecolor($watermark_width, $watermark_height);
			imagecopyresampled($new_water_img,$water_img,0,0,0,0,$watermark_width,$watermark_height,$water_file_info[0],$water_file_info[1]);
			
			
			if($gray)
				imagecopymergegray($new_img,$new_water_img,$water_x,$water_y,0,0,$watermark_width,$watermark_height,$pac);
			else
				imagecopymerge($new_img,$new_water_img,$water_x,$water_y,0,0,$watermark_width,$watermark_height,$pac);
			
			
			imagedestroy($new_water_img);
		}
		
		imagepng($new_img,$new_name);
		imagedestroy($new_img);
		
		return $new_name;
	}
	
	
	/*
	*图片读取
	*@param $path 文件路径
	*@param $type 读取到的文件类型  getimagesize返回的第三个参数
	*return 文件资源对象
	*/
	private function read_img($path,$type)
	{
		switch($type)
		{
			case self::GIF:
				$img = imagecreatefromgif($path);
				break;
			case self::JPG:
				$img = imagecreatefromjpeg($path);
			break;
			case self::PNG:
				$img = imagecreatefrompng($path);
			break;
			case self::BMP:
				$img = imagecreatefromwbmp($path);
			break;
			case self::WBMP:
				$img = imagecreatefromwebp($path);
			break;
			case self::XBM:
				$img = imagecreatefromxbm($path);
			break;
			case self::SWF:
			case self::PSD:
			case self::JPC:
			case self::JP2:
			case self::JPX:
			case self::JB2:
			case self::SWC:
			case self::IFF:
			
			default:
			$img = null;  
			break;
		}
		
		return $img;
	}
	
	
	
	/*
	*目录检测及创建
	*/
	private function checkDir()
	{
		$config = Config::getIns();
		$str = $config->thumb_pic_dir;

		if(empty($str)) return '未设置缩略图目录';
		if(!is_dir($config->thumb_pic_dir))
		{
			$res = _mkdir($config->thumb_pic_dir);
			if(!$res) return '目录创建失败';
		}
		
		return $str;
	}
	
	
	
	
	
}




