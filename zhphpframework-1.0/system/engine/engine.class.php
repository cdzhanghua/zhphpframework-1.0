<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
if(function_exists('spl_autoload_register')) {
    spl_autoload_register(array('engine', 'autoload'));
} else {
    function __autoload($class) {
        return engine::autoload($class);
    }
}
return engine::createApplication();#创建应用项目
final class engine {
    private function __construct() {}#防止被实例化
     public static $instance =array();
	 public static $loader_one=array();
     public static function autoload($class){
         if(! class_exists($class)){
             $_src=array(
                 ROOT_PATH.'config/',
				 ROOT_PATH.'core/',
                 ROOT_PATH.'engine/',
                 ROOT_PATH.'engine/factory/',
                 ROOT_PATH.'library/',
                 APP_PATH.'api/',
                 APP_PATH.'common/',
                 APP_PATH.'controllers/',
                 APP_PATH.'models/',
                 APP_PATH.'modules/controllers/',
                 APP_PATH.'modules/models/',
             );
           $_txt=array('.php','.class.php');
           foreach($_src as $resource){
                foreach($_txt as $txt){
                    $file=$resource . $class . $txt;
                    if(is_file($file)){
                        $filesha1=sha1_file($file);#避免重复加载将文件名名sha1()加密后存放在$GLOBALS['_LOADAPPFILENAME']数组总
                        if( ! isset($GLOBALS['_LOADAPPFILENAME'])){ $GLOBALS['_LOADAPPFILENAME']=array();}
                        $boolean=in_array($filesha1,$GLOBALS['_LOADAPPFILENAME']);
                        if($boolean === false){
                            include_once($file);
                            $GLOBALS['_LOADAPPFILENAME'][]=$filesha1;
                        }
                    }
                }
              }
           spl_autoload($class);
         }else{
             return true;
         }
     }

      /**
         * 创建初始目录
         * @return boolean|null
         */
     public static function createApplication(){
         if(is_dir(APP_PATH)){ return true; }
         $dirs=array(
             APP_PATH.'caches/',
             APP_PATH.'common/',
             APP_PATH.'config/',
             APP_PATH.'controllers/',
             APP_PATH.'controllers/components/',
             APP_PATH.'data/',
             APP_PATH.'data/session/',
             APP_PATH.'data/error_log/',
             APP_PATH.'data/cookie/',
             APP_PATH.'language/',
             APP_PATH.'models/',
             APP_PATH.'modules/admin/',
             APP_PATH.'modules/admin/controllers/',
             APP_PATH.'modules/admin/models/',
             APP_PATH.'modules/admin/views/',
             APP_PATH.'modules/admin/views/layout',
             APP_PATH.'modules/admin/views/Admin',
             APP_PATH.'modules/admin/controllers/components/',
             APP_PATH.'runtime/',
			 APP_PATH.'extends/',
             APP_PATH.'extends/vendors/',
			 APP_PATH.'extends/tools/',
             APP_PATH.'views/',
             APP_PATH.'views/layout/',
             APP_PATH.'views/Index/',
             APP_PATH.'widget/',
             APP_PATH.'api/',
             APP_PATH.'web/',
             APP_PATH.'web/themes/',
             APP_PATH.'web/themes/css/',
             APP_PATH.'web/themes/js/',
             APP_PATH.'web/themes/images/',
             APP_PATH.'web/themes/html/',
             APP_PATH.'uploads/');
         $isDir=file::mk_dir($dirs);#创建目录
         if($isDir){
           $isDemo=self::creatDemo();#创建demo
             if($isDemo){
                 self::createConf();#初始化配置文件
             }
         }
         unset($dirs,$isDemo,$isDir);
         return true;
      }
      /**
       * 脚本初始化
       * @return boolean
       */
      public static function createConf(){
         $dst=array('config/config.php','config/database.php','config/router.php','config/template.php');
         foreach ($dst as $file){
           @copy(ROOT_PATH.$file, APP_PATH.$file);
         }
          unset($dst);
         return true;
      }
      /**
       * 创建demo
       */
      public static function creatDemo(){
          $demo=self::load('Demo');
          demo::controllDemo();//#创建控制器demo
          unset($demo);
          return true;
      }
      /**
 * 应用框架程序单例实例化
 * @param type $object
 * @return type
 * @throws Exception
 */
    public static function load($class){
        $Object=self::getObject($class);
	    if(is_null($Object)){#就表明换成中没有对象
            if(class_exists($class)){
              $Object = new $class();#否则就实例化对象
              self::$instance[$class]=serialize($Object);#把对象序列化并cache起来
			  return $Object;
            }else{
			   die($class.'不存在');
			}
		}
        return $Object;   
    }
   /**
    * 获取已经实例化的类对象
    * @param type $class
    * @return type
    */
    public static function getObject($class){
	  return isset(self::$instance[$class])?unserialize(self::$instance[$class]):null;#如果存在就返回对象,否则就返回nul
    }
   /**
     * 系统函数自动调用,开发者的自己的函数需要手动调用
     * @param $path
     * @param string $func_name
     * @param null $args
     * 使用： @.xxxx   表示当前项目的common/的文件  如果有目录包含 @.folder.xxx
     *       否则就是系统提供的函数
     *  @代表当前项目
    * engine::loadCommon('test','function')
    *  将加载:  system/common/test.function.php
    * engine::loadCommon('@.test')
    * 将加载:  application/common/test.php
    * engine::loadCommon('test.test','function')
    * 将加载:  application/common/test/test.function.php
     */
    public static function loadCommon($s_fileName,$ex=null,$func_name=null, $args = null){
        $dirPath = substr_count($s_fileName, '@') > 0 ? APP_PATH. 'common/': ROOT_PATH.'common/';#得到文件路径
        $filepath = substr_count($s_fileName, '@') > 0 ?str_replace('@.', '', $s_fileName):$s_fileName;#得到文件名
        $file=substr_count($filepath, '.') > 0?str_replace('.','/',$filepath):$filepath;
        $fileName=is_null($ex)?$dirPath.$file.'.php':$dirPath.$file.'.'.$ex.'.php';
		if(is_file($fileName)){#判断文件是否存在
		      include_once $fileName;#判断文件是否加载如果没有就加载
              ###################是否自动执行方法##########################################
              if( ! is_null($func_name)){#判断参数中是否有方法名
                   if (function_exists($func_name)) {#如果存在方法
                       $callback = '';
                       if ( ! is_null($args) && is_array($args)) {
                           $callback = call_user_func_array($func_name, $args);
                       } elseif (is_string($args)) {
                           if(strpos($args,',') !== false){#如果是多个字符串 a,b,c 就转换成数组传递
                               $args = explode(',', $args);
                               $callback = call_user_func_array($func_name, $args);
                             }else{
                               $callback = call_user_func($func_name, $args);
                           }
                       }
                       unset($func_name,$args,$s_fileName,$dirPath,$filepath,$fileName,$isloader);
                       return $callback;#返回函数函数的执行结果
                   }
               }
           }
    }

    /**
     * 自动加载控制的组件文件
     */
    public static function loadComponent(){
       $componentsFile1=read_folder_directory(APP_PATH.'controllers/components/'); #自动加载前端控制下的compontens
       foreach($componentsFile1 as $file){
           $file=APP_PATH.'controllers/components/'.$file;
           if(is_file($file)){
               include_once $file;
           }
        }
    }
    /**
     * 手动调用extends 第三方扩展
     *  调用方式
     * 如果文件是 test.php
     *   engine::loadExtends('tools.bom')  调用框架的第三方扩展
     *   engine::loadExteneds('@.tools.test'); 调用应用程序的第三方扩展
     * 如果文件名是  test.class.php
     * engine::loadExteneds('@.tools.test','class'); 调用应用程序的第三方扩展
     *  语法解释:
     * @  代表当前项目
     * tools 代表 extends/tools/
     * test 代表  extends/tools/test.php
     */
     public  static  function  loadExtends($fileName,$ex=null){
         $dirPath = substr_count($fileName, '@') > 0 ? APP_PATH. 'extends/': ROOT_PATH.'extends/';#得到路径
         $filepath=substr_count($fileName, '@') > 0?str_replace('.','/',str_replace('@.', '', $fileName)):str_replace('.','/',$fileName);#得到文件
         $fileName=is_null($ex)?$dirPath.$filepath.'.php':$dirPath.$filepath.'.'.$ex.'.php';
         if(is_file($fileName)){#判断文件是否存在
              unset($dirPath,$filepath);
			  include_once $fileName;
         }
     }
}