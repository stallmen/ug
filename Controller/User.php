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
	
	
	public function verifyCode()
	{

	    $a = Mysql::getIns();

	    $b = $a->insert('test',['name'=>'张']);
//	    $b = $a->insert('test',[['name'=>'张','id'=>2],['name'=>'里','id'=>3]]);
	    var_dump($b);exit;

		$verify = Verify::getIns();
		$code_arr = $verify->verifyCode();
		if(!is_array($code_arr)) return false;

		
		if(!$this->redis->set($code_arr['token'],$code_arr['verifyCode'],120000)) return false;
		
		return json(['token'=>$code_arr['token'],'img_base64'=>$code_arr['img_base64']]);
	}
	
	
	public function login()
	{
	
		if(!empty($_POST['token']) && !empty($_POST['code']))
		{
			$code = $this->redis->get($_POST['token']);
			if(!$code) return json('验证码错误');
			
			if(strcasecmp($_POST['code'],$code) != 0) return json('验证码错误');
		}
		
		//登录\注册
		if(!empty($_POST['user']) && !empty($_POST['pwd']))
		{
			$model = new UserModel();
			$row = $model->mysql->query('select name from user where name=:user and pwd=:pwd',['user'=>$_POST['user'],'pwd'=>$_POST['pwd']],true);
			
			//登录
			if($row)
			{
				
			}else
			{
				$res = $model->mysql->query("insert into user (name,pwd) values (:name,:pwd)",['name'=>$_POST['user'],'pwd'=>$_POST['pwd']]);
				if($res)
					return json('注册成功');
				else
					return json('注册失败');
			}
		}
		
		
		
		return view('login.html');
	}
	
	
}



