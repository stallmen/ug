<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');

class Redis
{
	private static $ins;
	private $config;
	private $redis;
	
	final private function __construct()
	{
		if(!extension_loaded('igbinary') || !extension_loaded('redis'))  die('the redis class need igbinary and redis extension!');
		
		$this->config = (Config::getIns())->redis;
		$this->connect();
		
	}
	
	
	final private function __clone(){}
	
	
	
	public static function getIns()
	{
		if(!self::$ins instanceof self) self::$ins = new self();
		
		return self::$ins;
	}
	
	
	public function connect()
	{
		if(empty($this->config)) die('please set redis connection args first!');
		
		$redis = new \Redis();
		try
		{
			$conn = $redis->connect($this->config['host'],$this->config['port']);
			
		}catch(\Exception $e)
		{
			die('redis connect fail,please check you redis-server!');
		}
		
		$this->redis = $redis;
		return;
	}
	
	/*
	*字符串写入
	*@param  $flag 数组标识/为数组启用压缩
	*@param  $expire_time 毫秒级过期时间
	*默认使用igbinary压缩
	*/
	public function set($key,$value,$expire_time = 0,$flag = false)
	{
		if($flag) $value = igbinary_serialize($value);
		
		if($expire_time)
		{
			$res = $this->redis->psetex($key,$expire_time,$value);
		}else
		{
			$res = $this->redis->set($key,$value);			
		}
		
		if(!$res) return false;
		
		return true;
	}
	
	
	/*
	*读取字符串
	*
	*/
	public function get($key)
	{
		return $this->redis->get($key);
	}
	
	
	
	
	
}

