<?php

/*
 *Mysql数据操作类
 */

class Mysql
{
    private static $ins = null;

    private $mysql;
    private $conn = null;
    private $statement;
    
    final private function __construct()
    {
        $this->mysql = (Config::getIns())->mysql; 
        $this->connect();



    }


    final private function __clone(){}



    public static function getIns()
    {
        if(!self::$ins instanceof self)  self::$ins = new self;

        return self::$ins;
    }


    private function connect()
    {
        if(empty($this->mysql)) return;

        $dsn = 'mysql:dbname=' . $this->mysql['db'] . ';host=' . $this->mysql['host'];        
        
        try
        {
            $this->conn = new PDO($dsn,$this->mysql['user'],$this->mysql['pwd']);
        }catch(PDOException $e)
        {
            if(DEBUG)   throw new Exception($e->getMessage());

            die('new pdo error!');
        }

        return $this->conn;
    }


    public function query($sql,$data = [],$one = false,$pattern = PDO::FETCH_ASSOC)
    {
        $sql = trim($sql);

        if(empty($sql)) return false;

        //$data与$one可互换位置
        if(is_scalar($data))
        {
           list($one,$data) = [$data,[]]; 
        }  
        
        //预处理语句
        $this->statement = $this->conn->prepare($sql);
        
        if(!empty($data) && is_array($data))
        {
             //数据绑定前操作
            $keys = array_keys($data);

            $res =  array_walk($keys,function(&$v,$k)
            {
                $v = ':' . $v;
            });

            if(!$res) die('data error!');

            $data = array_combine($keys,$data);             //最终的绑定数据
  
        
        }
        //检测操作select insert up...  etc
        $position = strpos($sql,' ');
        if(false === $position) die('sql error!');

        $begin = strtolower(substr($sql,0,$position));
        
        //删改操作没有where条件拒绝执行
        if($begin == 'update' || $begin == 'delete')
        {
            if(strpos($sql,'where') === false)  die('please confirm your sql,you dont have where condition!');
        }
        
        
        $final = $this->statement->execute($data);
        if(true !== $final)
        {
            ob_start();
            $this->statement->debugDumpParams();
            $ob_out = ob_get_clean();
            Log::write($ob_out);
            if(DEBUG)
            {
                die($ob_out);
            }else
            {
                die('sql execute fail!');
            }
        }
        

                 

        //不同操作不同返回
        switch($begin)
        {
            case 'select':
                if($one == true)
                {
                    return $this->statement->fetch($pattern);
                }else
                {
                    return $this->statement->fetchAll($pattern);
                }
            
            break;
            case 'insert':
                return $this->getLastInsertId();

            break;
            default:
                return $this->statement->rowCount();

            break;
        }
    }
    

    private function getLastInsertId()
    {
        return $this->conn->lastInsertId();
    }


    /*
     *
     *事务封装
     */

    public function beginTransaction()
    {
       $this->conn->beginTransaction(); 
    }



    public function commit()
    {
        $this->conn->commit();
    }

    public function rollBack()
    {
        $this->conn->rollBack();
    }


    





}




