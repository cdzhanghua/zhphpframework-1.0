<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class httpRequest {
    /**
     * 判断是否是get提交数据
     * @return type
     */
    public static  function isGet(){
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'get') ? true : false;
    }
    /**
     * 获取get值
     * @param type $key
     * @return type
     */
    public static function get($key){
        if(self::isGet()){
            $getVal=isset($_GET[$key])?$_GET[$key]:null;
            unset($key);
            $getVal=filterKword($getVal);
            return addslashes($getVal);
        }
    }
    /**
     * 判断是否是post提交数据
     * @return type
     */
    public static  function isPost(){
        return (strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? true : false;
    }
    /**
     * 获取post提交的数据
     * @param type $data
     */
    public static function post($data=array()){
        if(self::isPost()){
            $data=empty($data)?$_POST:$data;
            foreach ( $data as $key => $value ){
                if (get_magic_quotes_gpc()){
                    $value = htmlspecialchars( stripslashes((string)$value));
                }else{
                    $value = htmlspecialchars( addslashes((string)$value) );
                }
                $data[$key]=$value;
            }
            return $data;
        }
    }

    /**
     * 判断是否是ajax
     * @return boolean
     */
    public static function isAjax(){
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ==
            'xmlhttprequest'){
            return true;
        }
    }


    /**
     * url重定向
     * @param null $url
     * @param int $status
     */
    public static  function redirect($url = null, $status = 302){
        header('Status: ' . $status); #header头发送状态status信息为 302#
        header('Location: ' . APP_URI.str_replace('&amp;', '&', $url));
        exit();
    }
    /**
     * ajax返回
     * @param unknown_type $ajaxReturnType  返回的类型  默认为 string  可选有 json
     * @param unknown_type $ajaxReturnData  返回的数据  注意： 如果是xml 或者 json 那么数据必须是数组
     */
    public static function ajaxReturn(&$ajaxReturnData = null,$ajaxReturnType = 'string'){
        if ($ajaxReturnType == 'string'){
            return $ajaxReturnData;
        }elseif ($ajaxReturnType == 'json'){
            foreach ($ajaxReturnData as $key => $value){
                if (is_array($value)){
                    self::ajaxReturn( $value,'json');
                }
                $json_value = urlencode($value);
                $jsonstr = json_encode($json_value);
                $json = urldecode($jsonstr);
            }
            return $json;
        }
    }

    /**
     * 返回来路的url 可以做返回的链接
     * @return string
     */
    public  static function  http_referer(){
        return htmlspecialchars($_SERVER['HTTP_REFERER']);
    }

    /**检测是否为指定来路
     *
     * @param $strDomain
     * @return bool
     */
    public static  function ValidateReferer($strDomain)
    {
        // Make domains
        $strDomain = str_replace("www.", "", $strDomain);

        // Check
        if (strstr($_SERVER["HTTP_REFERER"], $strDomain))
        {
            return true;
        }

        return false;
    }

     /**
      *  错误重定向页面
      */
      public static    function  error(){

      }



    /**
     * 获取url段
     * @param $key
     * @param $value
     */
     public static function segment($segment=''){
             $module=$GLOBALS['router']->getModule();#获取模块
             $control=ucfirst($GLOBALS['router']->getControl());#获取控制器
             $action=$GLOBALS['router']->getAction();#获取动作名
             $module=empty($module)?null:$module;
             $control=empty($control)?'index':$control;
             $action=empty($action)?'index':$action;
          if($segment == ''){
              $segment=array($module,$control,$action);
          }else{
              switch ($segment){
                  case 'm': $segment=$module;break;
                  case 'c': $segment=$control;break;
                  case 'a': $segment=$action;break;
              } 
          }
          return $segment;
     }


    public static  function  addSession($key, $value)
    {
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session->addSession($key, $value);
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session->addSession($key, $value);
        }
        unset($session);
    }

    public static  function  setSession($key, $value)
    {
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session->setSession($key, $value);
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session->setSession($key, $value);
        }
        unset($session);
    }

    public static  function  getSession($key)
    {
        $session_val='';
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session_val=$session->getSession($key);
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session_val=$session->getSession($key);
        }
        unset($session);
        return $session_val;
    }

    public static  function  getAllSession()
    {
        $session_val=array();
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session_val=$session->getAllSession();
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session_val=$session->getAllSession();
        }
        unset($session);
        return $session_val;
    }

    public static  function  unsetSession($key)
    {
        $session_val=array();
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session_val=$session->unsetSession($key);
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session_val=$session->unsetSession($key);
        }
        unset($session);
    }

    public static  function  unsetAllSession()
    {
        $handler= config::readConfig('session','save_handler');
        if($handler == 'file' || $handler == '' ){
            $session=new fileSession();
            $session_val=$session->unsetAllSession();
        }else if($handler == 'mysql' || $handler == 'db'){
            $session=new dbSession();
            $session_val=$session->unsetAllSession();
        }
        unset($session);
    }
    public static   function  addCookie($cookie_name, $value){
        return cookie::addCookie($cookie_name, $value);
    }
    public  static  function setCookie($cookie_name, $value){
     return cookie::setCookie($cookie_name, $value);
    }
    public static  function  getCookie($cookie_name){
     return cookie::getCookie($cookie_name);
   }
   public  static  function getAllCookie(){
    return cookie::getAllCookie();
   }
  public  static  function  unsetCookie($cookie_names){
   return cookie::unsetCookie($cookie_names);
  }
    public static  function unsetAllCookie(){
     return cookie::unsetAllCookie();
    }
}