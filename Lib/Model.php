<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');

abstract class Model
{
	public $mysql;
	
	public function __construct()
	{
		$this->mysql = Mysql::getIns();
	}

	
	
	
	
	
	
}



