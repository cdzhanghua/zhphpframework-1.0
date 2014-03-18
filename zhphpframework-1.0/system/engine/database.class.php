<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */

final class database{
    public static $connect=null;#数据库链接对象
    private $host;#数据库服务器地址
    private $user;#数据库用户名
    private $pwd;#数据库密码
    private $dbName;#数据库名
    private $port;#数据库服务器端口
    private $charset;#数据库默认编码
 
    public static  function init(){
      $database=engine::load('database');#单例模式实例化database类返回对象
      $dbConf=config::readConfig('db');#得到数据库配置文件
      #依据服务器配置判断引擎 
      if(extension_loaded('pdo_mysql')){
           if( ! isset(config::$confVal['DbConnection'])){#单例模式
              $pdo=$database->pdoLink($dbConf);
              config::writeConfig('DbConnection',$pdo);#将对象写入配置文件中，可供全局调用，这也是提供sql 原生态操作对象的一种方式
			  unset($pdo);
          }
          return engine::load('dbPdo');
       }else if(extension_loaded('mysqli')){
           if( ! isset(config::$confVal['DbConnection'])){#单例模式
             $mysqli=$database->mysqliLink($dbConf);
              config::writeConfig('DbConnection',$mysqli);#将对象写入配置文件中，可供全局调用，这也是提供sql 原生态操作对象的一种方式
			  unset($mysqli);
          }
           return engine::load('dbMysqli');
        }else{
         $mysqlConnResource=$database->mysqlLink($dbConf);
          return $mysqlConnResource;
      }
  }
   private  function pdoLink(&$dbConf){
         $dsn = 'mysql:host='.$dbConf['db_host'].';port='.$dbConf['db_prot'].';dbname='.$dbConf['db_database'];
        $username = $dbConf['db_name'];
        $password = $dbConf['db_password'];
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$dbConf['db_charset'],
        ); 
     try {
          $pdo=new PDO($dsn, $username, $password, $options);
		  unset($dsn, $username, $password, $options,$dbConf);
          return $pdo;
      } catch (PDOException $pdoExce) {
         die('服务器链接失败');
    }
 }
   private function mysqliLink(&$dbConf){
        @$mysqli=new mysqli($dbConf['db_host'].':'.$dbConf['db_prot'], $dbConf['db_name'], $dbConf['db_password'],$dbConf['db_database']);
           if(@$mysqli->connect_error){
               $mysqli->set_charset($dbConf['db_charset']);
               unset($dbConf);
               return $mysqli;
            }else{
               die('数据库服务器连接失败!');
           }
   }
   private function mysqlLink(&$dbConf){
       $link=mysql_connect($dbConf['db_host'].':'.$dbConf['db_prot'],$dbConf['db_name'],$dbConf['db_password']) or die('error_log: 数据库服务器链接失败!');
       mysql_select_db($dbConf['db_database'], $link) or die('error_log: 访问的数据库不存在');
       mysql_set_charset($dbConf['db_charset'],$link)  or die('error_log: 数据库编码设置失败');
       unset($dbConf);
       return $link;
   }
   public static function close(){
       $db=config::readConfig('DbConnection');
       if(is_object($db)){
           $className=get_class($db);
            if($className == 'PDO'){
                $db=null;
             }else if($className == 'mysqli' ){
                $db->close();
            }
       }else if(is_resource($db)){
             mysql_close($db);
       }
   }

}