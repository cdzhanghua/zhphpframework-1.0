<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class dispatcher {
    private function __construct() {}#防止被实例化
    public static function dispatch($router){
          #获取路由
         $module=''.$controller=''.$action=''.$params='';
         $module=$router->getModule();#得到模块
         $module=empty($module)?'':'modules/'.$module.'/';#构建模块
         $control=$router->getControl();#得到控制器名
         $controller=ucfirst($control).'Controller';#获取控制器
         $actionName=$router->getAction();#得到用户的action
         $action=$actionName.'Action';#组装真实的action
         $params=is_array($router->getParams())?$router->getParams():array();#获取参数
         $controllerfile=APP_PATH.$module .'controllers/'.$controller.'.php';#构建控制器文件
        if( is_file($controllerfile)){#判断是否存在该文件
		############################避免重复加载文件开始###########################################################
		 $filesha1=sha1_file($controllerfile);#避免重复加载将文件名名sha1()加密后存放在$GLOBALS['_LOADAPPFILENAME']数组总
         if( ! isset($GLOBALS['_LOADAPPFILENAME'])){ $GLOBALS['_LOADAPPFILENAME']=array();}
         $boolean=in_array($filesha1,$GLOBALS['_LOADAPPFILENAME']);
         if($boolean === false){$GLOBALS['_LOADAPPFILENAME'][]=$filesha1; }
		 ###########################################################################
		 if(APP_DEBUG == false){#页面静态化,那么就必须将debug 设为false--因为这是上线状态
		       ################## pate static  start ######################################
		       $adminModule=config::readConfig('app','admin_module');
               if($adminModule != $module){
                    $is_read=self::readhtml($module,$control,$actionName,$params);#静态化处理
					 if($is_read === false){
					    self::run($controllerfile,$controller,$action,$actionName,$params);
						self::writeHtml($module,$control,$actionName,$params);#静态化处理
					 }
					################## pate static  end ###################################### 
					}else{
					 ############ pate not static start##############
					  self::run($controllerfile,$controller,$action,$actionName,$params);
					 ##############pate not static end##########
					}
				}else{
				   #调试模式运行start
                        self::run($controllerfile,$controller,$action,$actionName,$params);
					#调试模式运行end
				 }
			}else{
               header("HTTP/1.0 404 Not Found");#404错误
               }
   unset($filesha1,$controllerfile,$module,$control,$controller,$action,$params,$filesha1,$boolean,$actionName,$router,$adminModule);
    }
    /**
     * 自动实现页面静态化
     */
    public static function  readhtml($module,$controller,$action,$params){
        #目录组装
        $htmlFile=HTML_STATIC_PATH.$module.$controller.'/'.$action;
        #参数链接
        $countParams= count($params);
        if($countParams){
            foreach ($params as $value){
              $htmlFile.='_'.$value;
            }
         }
         $htmlFile.='.html';
        /**
         * 第一次运行创建目录并生成文件
         */
        if(file_exists($htmlFile)){
            echo (file_get_contents($htmlFile));
		    die();
          }else{
		    return false;
		  }
     }
     public  static function writeHtml($module,$controller,$action,$params){
          #目录组装
        $htmlFile=HTML_STATIC_PATH.$module.$controller.'/'.$action;
        #参数链接
        $countParams= count($params);
        if($countParams){
            foreach ($params as $value){
              $htmlFile.='_'.$value;
            }
         }
         $htmlFile.='.html';
          $dir=dirname($htmlFile);
          mk_dir($dir);
          $content=ob_get_contents();
          file_put_contents($htmlFile, $content);
     }
public  static function run($controllerfile,$controller,$action,$actionName,$params){
    include_once ($controllerfile);#加载文件
    if(class_exists($controller)){ #处理用户请求
       $controlObj=engine::load($controller);#实例化控制器对象
        if(method_exists($controlObj,$action)){
            $before='before_'.$actionName;  #是否有在什么之前 
            $after='after_'.$actionName;#在之后的操作
            if(method_exists($controlObj,$before)){#存在在什么之前
               call_user_func_array(array(&$controlObj,$before),$params);#执行用户的请求
            }
               call_user_func_array(array(&$controlObj,$action),$params);#执行用户的请求
            if(method_exists($controlObj, $after)){#存在在什么之后
               call_user_func_array(array(&$controlObj,$after),$params);#执行用户的请求
            }
            $_GET=$params;#把参数给get
          }else{
           header("HTTP/1.0 404 Not Found");#404错误 
         }
      }else{
          header("HTTP/1.0 404 Not Found");#404错误  
      }
   }
}