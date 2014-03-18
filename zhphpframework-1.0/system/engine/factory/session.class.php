<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-3-8
 * Time: 下午3:15
 * To change this template use File | Settings | File Templates.
 */
abstract  class session {
    #外部接口
    abstract  public  function  addSession($key,$value);
    abstract  public  function  setSession($key,$value);
    abstract  public  function  getSession($key);
    abstract  public  function  getAllSession();
    abstract  public  function  unsetSession();
    abstract  public  function  unsetAllSession();
}