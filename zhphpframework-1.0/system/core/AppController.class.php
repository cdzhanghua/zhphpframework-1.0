<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */

class AppController extends controller   {
    protected  function  checkform($formhash){
         $module=$GLOBALS['router']->getModule();
         $control=$GLOBALS['router']->getControl();
         $action=$GLOBALS['router']->getAction();
         $module=empty($module)?null:$module.'/';
         $url=HTTP_URL.$module.$control.'/'.$action;
         $key=$key=config::readConfig('session','key_token').substr($_SERVER['REQUEST_TIME'],0,-7);
         $hash=token::encrypt($url, $key);
         if($formhash == $hash){
             return true;
         }
            return false;
      }
}