<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class config{
    public static $confVal=array();
    public static $zhConf=array();
    private function __construct() {}#防止被实例化
    /**
     * 预加载配置文件
     */
    public static function loadConfig(){
         $configFile1=read_folder_directory(ROOT_PATH.'config/');
         $configFile2=read_folder_directory(APP_PATH.'config/');
         foreach ($configFile2 as $file){
               self::$confVal[]= include_once APP_PATH.'config/'.$file;
         }
          foreach ($configFile1 as $file){
               self::$zhConf[]= include_once ROOT_PATH.'config/'.$file;
         }
        unset($configFile1,$configFile2);
 }
    /**
     * 写配置文件
     * @param type $key
     * @param type $value 
     */
     public  static function writeConfig($key,$value){
        if(array_key_exists($key, self::$confVal) === false){
             self::$confVal[$key]=$value;
          }
       }
       /**
        *读取配置文件
        * @param type $key
        * @param type $twoKey
        * @return type 
        */
     public static function readConfig($key,$twoKey=null){
         if(array_key_exists($key, self::$confVal)){
              return (is_null($twoKey))?self::$confVal[$key]:self::$confVal[$key][$twoKey];
         }
        foreach (self::$confVal as $array){
               if(array_key_exists($key,$array)){
                  return (is_null($twoKey))?$array[$key]:$array[$key][$twoKey];
                  break;
                }
           } 
     }
}