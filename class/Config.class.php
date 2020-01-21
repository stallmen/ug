<?php

/*
 *
 *配置读取类
 *
 */

class Config
{
	protected static $ins = null;

	protected $config = [];


	final protected function __construct()
	{
		$this->config = require(ROOT . '/config/config.php');
		

	}

	final protected function __clone(){}



	public static function getIns()
	{
		if(self::$ins instanceof self) return self::$ins;
		else
		{
			self::$ins = new self;
			return self::$ins;
		}
	}


	
	public function __get($str)
	{
		if(array_key_exists($str,$this->config))		return $this->config[$str];
		else							return [];
	}
	
	/*
	 *仅支持数组赋值
	 */

	public function __set($key,$value)
	{

		if(!is_array($value)) return false;

		if(empty($this->config[$key])) 			$this->config[$key] = [];

		$this->config[$key] = array_merge($this->config[$key],$value);
		return $this->config[$key];
	}
	

}



