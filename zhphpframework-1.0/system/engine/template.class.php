<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class template{
    private static $loadSmartyArr=array();
    #防止被实例化
    private  function __construct() { return; }

    /**
     * @return mixed|Smarty
     */
    private static function loadSmarty(){
         if(isset(self::$loadSmartyArr['Smarty'])){
               return unserialize(self::$loadSmartyArr['Smarty']);
          }else{
                engine::loadExtends('vendors.Tpl.Smarty.Smarty','class');
                $smarty=new Smarty();
                self::$loadSmartyArr['Smarty']=  serialize($smarty);
                return $smarty;
          }
      }
      /**
       * 设置模版变量
       * @param type $template_dir
       * @return type
       */
    public static function setTpl(){
           $smarty=self::loadSmarty();
           $router=engine::getObject('router');
           $smartyConf=config::readConfig('tpl');
           $module=$router->getModule();#获取模块
           $module=empty($module)?null:$module.'/';
         if(empty($smartyConf['template_dir'])){
            $template_dir=empty($module)?APP_PATH.'views/':APP_PATH.'modules/'.$module.'views/';#得到模版目录地址
         }else{
            $template_dir=empty($module)?APP_PATH.$smartyConf['template_dir'].'/':APP_PATH.'modules/'.$module.$smartyConf['template_dir'].'/';#得到模版目录地址
          }
         $cache_dir= empty($smartyConf['cache_dir'])?APP_PATH.'caches/':APP_PATH.$smartyConf['cache_dir'].'/';
         $compile_dir= empty($smartyConf['compile_dir'])?APP_PATH.'runtime/':APP_PATH.$smartyConf['compile_dir'].'/';
         #配置smarty 参数
           $smarty->setTemplateDir($template_dir); #配置模板目录
           $smarty->compile_check=$smartyConf['compile_check'];#配置是否每次编译之前都检查是否有更新
           $smarty->setCompileDir($compile_dir);#smarty模板编译目录
          if($smartyConf['caching']){
             $smarty->caching=$smartyConf['caching'];#是否缓存
             $smarty->cache_lifetime=$smartyConf['cache_lifetime'];#缓存过期时间
             $smarty->setCacheDir($cache_dir);#缓存目录
           }
           $smarty->left_delimiter=$smartyConf['left_delimiter'];#左边界符号
           $smarty->right_delimiter=$smartyConf['right_delimiter'];#右边界符号
           $smarty->allow_php_templates=true;
           #$Smarty->allow_php_tag=true;#
           unset($smartyConf,$template_dir,$router,$module,$cache_dir,$compile_dir);
          return $smarty;
       }
}