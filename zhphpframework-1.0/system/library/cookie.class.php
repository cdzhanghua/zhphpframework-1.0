<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class cookie {

    public static function  addCookie($cookie_name, $value)
    {
        setcookie($cookie_name,$value,time()+3600*24,APP_PATH.'data/cookie/');
    }

    public static function  setCookie($cookie_name, $value)
    {
         return (is_null(self::getCookie($cookie_name)))?setcookie($cookie_name,$value,time()+3600*24,APP_PATH.'data/cookie/'):setcookie($cookie_name,$value,time()+3600*24,APP_PATH.'data/cookie/');
    }

    public static function  getCookie($cookie_name)
    {
        return (isset($_COOKIE[$cookie_name])) ? $_COOKIE[$cookie_name] : null;
    }

    public static function  getAllCookie()
    {
        return $_COOKIE;
    }

    public static function  unsetCookie()
    {
       $cookies = func_get_args();
            foreach($cookies as $cookie) {
                if(self::getCookie($cookie)) {
                    setcookie($cookie,'del',time()-3600,APP_PATH.'data/cookie/');
            }
        }
     }

    public static function  unsetAllCookie()
    {
        $cookies = func_get_args();
        foreach($cookies as $cookie) {
            if(self::getCookie($cookie)) {
                setcookie($cookie,'del',time()-3600,APP_PATH.'data/cookie/');
            }
        }
        $_COOKIE=array();
        unset($_COOKIE);
    }

    /**
     * 设置cookie跨域
     */
    public static  function cookie_domain(){
        header("P3P: CP=CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR");
     }
}