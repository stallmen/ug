<?php

/*
 *
 *日志类
 *
 */

class Log
{

        private static $ins = null;

        //日志文件名
        private static $log = 'log.txt';
        //路径
        private static $path = '';


        final private function __construct()
        {
                $this->getRoot();
        }


        final private function __clone()
        {


        }



        private static function getIns()
        {
                if(!self::$ins instanceof self) self::$ins = new self;

                return self::$ins;


        }

        /*
         *获取文件路径
         */

        private function getRoot()
        {
                self::$path = ROOT . 'log/' . date('Ymd') . '/';
                return self::$path;
        }




        /*
         *检验文件,目录是否存在
         */
        private static function fileExists()
        {
                if(!file_exists(self::$path))     mkdir(self::$path,0777,true);
        }




        /*
         *检测文件大小,如果大于2M,重命名再另起一个
         */
        private static function testSize()
        {
                if(!file_exists(self::$path . self::$log)) touch(self::$path . self::$log);

                //清楚文件大小缓存
                clearstatcache(true,self::$path . self::$log);


                if(filesize(self::$path . self::$log) >= 2097152)
                {
                        rename(self::$path . self::$log , self::$path . 'log' . mt_rand() . '.txt');

                        touch(self::$path . self::$log);
                }
        }


        /*
         *写入文件
         */
        public static function write($str)
        {
                self::getIns();
                self::fileExists();
                self::testSize();


                $rs = @fopen(self::$path . self::$log,'ab');
                fwrite($rs,PHP_EOL . $str . PHP_EOL);
                fclose($rs);


        }




}
