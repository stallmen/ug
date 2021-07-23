<?php
namespace Controller;
use Lib\Controller;
use Lib\Mysql;
use Lib\Verify;
use Lib\Redis;

class User extends Controller
{
	private $redis;
	
	
	public function __construct()
	{
		parent::__construct();
		
//		$this->redis = Redis::getIns();
	}
	

}



