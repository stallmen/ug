<?php
namespace Lib;
defined('WHO_YOU_ARE') || die('access deny!');
/*
 *Mysql数据操作类
 */

final class Mysql
{
    private static $ins = null;

    private $mysql;
    private $conn = null;
    private $statement;
    
    final private function __construct()
    {
        if(!extension_loaded('pdo'))  die('the pdo extension not loaded!');

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
        if(empty($this->mysql)) return false;

        $dsn = 'mysql:dbname=' . $this->mysql['dbname'] . ';host=' . $this->mysql['host'] . ';charset=' . $this->mysql['charset'];
        
        try
        {
            $this->conn = new \PDO($dsn,$this->mysql['user'],$this->mysql['pwd'],[\PDO::ATTR_EMULATE_PREPARES => false]);
        }
        catch(\PDOException $e)
        {
            if(DEBUG)   throw new \Exception($e->getMessage());

            die('new pdo error!');
        }

        return $this->conn;
    }

    /*
     *@params sql SQL语句
     *@params data 绑定参数
     *@params one true返回单条|false返回多条
     *@params pattern 模式
     */
    public function query($sql,$data = [],$one = false,$pattern = \PDO::FETCH_ASSOC)
    {
        $sql = trim($sql);
        if(empty($sql)) return false;


        //$data与$one可互换位置
        if(is_scalar($data))    list($one,$data) = [$data,$one];


        //预处理语句
        $this->statement = $this->conn->prepare($sql);

        if(!empty($data) && is_array($data))
        {
            //判断占位符类型
            if(!is_int(key($data)))
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
        }


        $final = $this->statement->execute($data);
        if(true !== $final)
        {
            ob_start();
            $this->statement->debugDumpParams();
            $ob_out = ob_get_clean();
            Log::write($ob_out);

            if(DEBUG)
                die($ob_out);
            else
                die('未知错误,请联系管理员!');
        }

        preg_match('/(select|update|delete|alter){1}/iU',$sql,$match);

        if(!empty($match[1]))
        {
            if(strcasecmp($match[1],'select'))
            {
                if($one == true)
                {
                    return $this->statement->fetch($pattern);
                }else
                {
                    return $this->statement->fetchAll($pattern);
                }
            }
            else
            {
                return $this->statement->rowCount();
            }
        }
        else
        {
            return $this->statement->fetchAll($pattern);
        }
    }

    /*
     * @params table 表名
     * @params data 插入的数据,支持批量插入
     * return 受影响行数
     */
    public function insert($table = '',$data = [])
    {
        if(empty($table) || !is_scalar($table) || empty($data) || !is_array($data)) return false;

        $sql = 'insert into ' . $table;


        $final_data = [];
        $values_sql = '';


        //单个插入
        if(!is_int(key($data)))
        {
            $keys = array_keys($data);

            $values_sql .= '(';
            foreach($data as $k=>$v)
            {
                $k = ':' . $k;
                $final_data[$k] = $v;

                $values_sql .= $k . ',';

            }

            $values_sql = rtrim($values_sql,',') . ')';

        }
        else
        {
            $keys = array_keys($data[0]);

            foreach($data as $v)
            {
                $values_sql .= '("';
                $values_sql .= implode('","',array_values($v));
                $values_sql .= '"),';

            }

            $values_sql = rtrim($values_sql,',');
        }

        $sql .= ' (' . implode(',',$keys) . ') values ' . $values_sql;

        //预处理语句
        $this->statement = $this->conn->prepare($sql);
        $final = $this->statement->execute($final_data);

        if($final === true)
        {
            return $this->getLastInsertId();
        }
        else
        {
            ob_start();
            $this->statement->debugDumpParams();
            $ob_out = ob_get_clean();
            Log::write($ob_out);

            if(DEBUG)
                die($ob_out);
            else
                die('未知错误,请联系管理员!');
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

    public function transaction()
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




