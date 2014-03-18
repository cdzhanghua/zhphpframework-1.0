<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class fileSession extends  session {
    private $_expiry=3600;#默认的有效期
    private $_domain;#有效域名
    private $_prefix;#有效前缀
    public  function  __construct(){
        $this->_domain=isset($GLOBALS['_SERVER']['HTTP_HOST'])?$GLOBALS['_SERVER']['HTTP_HOST']:$_SERVER['HTTP_HOST'];
        $this->_prefix=sha1(config::readConfig('sesssion','key_token'));
        ini_set('session.use_trans_id', 0);
        ini_set('session.gc_maxlifetime', $this->_expiry);
        ini_set('session.use_cookie', 1);
        ini_set('session.cookie_path', APP_PATH.'data/session/');
        ini_set('session.cookie_domain', $this->_domain);
    }

    public function  addSession($key, $value)
    {
        // TODO: Implement addSession() method.
      return  $_SESSION[$this->prefix][$key] = $value;
    }

    public function  setSession($key, $value)
    {
        // TODO: Implement setSession() method.
       return (is_null($this->getSession($key)))?$_SESSION[$this->prefix][$key] = $value:$_SESSION[$this->prefix][$key] = $value;

     }

    public function  getSession($key)
    {
        // TODO: Implement getSession() method.
        return ((isset($_SESSION[$this->prefix][$key])) ? $_SESSION[$this->prefix][$key] : null);
    }

    public function  getAllSession()
    {
        // TODO: Implement getAllSession() method.
        return $_SESSION['$this->prefix'];
    }

    public function  unsetSession()
    {
        // TODO: Implement unsetSession() method.{
           $session_vars = func_get_args();
           foreach($session_vars as $session_var) {
               if($this->get($session_var)||is_array($this->get($session_var))) {
                   $_SESSION[$this->prefix][$session_var]=null;
                   unset($_SESSION[$this->prefix][$session_var]);
               }
           }
     }

    public function  unsetAllSession()
    {
        // TODO: Implement unsetAllSession() method.
        $_SESSION=array();
        session_destroy();
    }
}