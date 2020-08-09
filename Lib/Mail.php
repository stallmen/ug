<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');


/*
*邮件发送类
*/


class Mail
{
	public static $ins;
	
	private $socket;
	private $config;   //配置信息
	private $mail_from; 
	
	
	final private function __construct()
	{
		if(!extension_loaded('sockets'))  die('the sockets extension not loaded!');
		
		$this->config = (Config::getIns())->mail;
		
		if(empty($this->config)) die('mail config not found');
	
		
		$this->mail_from = $this->config['user'];
		
		if(false == ($this->config['user'] = base64_encode($this->config['user'])) || false == ($this->config['pwd'] = base64_encode($this->config['pwd'])))  die('user,pwd can not be base64_encode');
		
		
		$this->connect();
		
	}
	
	final private function __clone(){}
	
	
	
	
	public static function getIns()
	{
		if(!self::$ins instanceof self) self::$ins = new self();
		
		return self::$ins;
	}
	
	
	/*
	*socket连接
	*/
	public function connect()
	{
		$this->socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
		if(false === $this->socket) $this->error();
		
		if(false === socket_connect($this->socket,$this->config['server'],$this->config['port'])) $this->error();
		
		socket_read($this->socket,1024);
		
	}
	
	/*
	*socket关闭
	*/
	public function close()
	{
		socket_close($this->socket);
	}
	
	
	/*
	*邮件发送
	*@param $from 发送者名字
	*@param $receive 接收者邮箱
	*@param $subject 主题
	*@param $data 邮件内容
	*/
	public function send($from,$receive,$subject,$data)
	{
		$body = 'FROM:' . $from . '<' . $this->mail_from . '>' . PHP_EOL;
		$body .= 'TO:<' . $receive . '>' . PHP_EOL;
		$body .= 'SUBJECT:' . $subject . str_repeat(PHP_EOL,2);
		$body .= $data . PHP_EOL . '.' . PHP_EOL;

		
		$data = [
			['HELO SMTP' . PHP_EOL,'250'],
			['AUTH LOGIN' . PHP_EOL,'334'],
			[$this->config['user'] . PHP_EOL,'334'],
			[$this->config['pwd'] . PHP_EOL,'235'],
			['MAIL FROM:<' . trim($this->mail_from) . '>' . PHP_EOL,'250'],
			['RCPT TO:<' . trim($receive) . '>' . PHP_EOL,'250'],
			['DATA' . PHP_EOL,'354'],
			[$body,'250'],
			['QUIT' . PHP_EOL,'221'],
		];

		foreach($data as $v)
		{
			if(false === socket_write($this->socket,$v[0])) $this->error();
		
			if(false === strpos(socket_read($this->socket,1024),$v[1])) $this->error();
			
		}
		
		$this->close();
		
		return true;
		
	}
	
	/*
	*关闭
	*/
	public function error()
	{
		die('socket fail,error message:' . socket_strerror(socket_last_error())); 
	}
	
	
	
	
	
	
	
}








