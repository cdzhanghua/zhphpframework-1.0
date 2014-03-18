<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */

class component {
    protected function __construct()
    {
        // TODO: Implement __construct() method.
    }

    protected function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    protected function __call($name, $arguments)
    {
        die($name.'没有找到');
    }

    protected static  function __callStatic($name, $arguments)
    {
        die('静态'.$name.'没有找到');
    }

    protected function __get($name)
    {
        return isset($this->$name)?$this->$name:null;
    }

    protected function __set($name, $value)
    {
        return  isset($this->$name)?$this->$name=$value:false;
    }

    protected function __isset($name)
    {
        return  isset($this->$name)?$this->$name:null;
    }

    protected function __unset($name)
    {
       if( isset($this->$name)){
           $this->$name=null;
           unset($this->$name);

       }
    }
}