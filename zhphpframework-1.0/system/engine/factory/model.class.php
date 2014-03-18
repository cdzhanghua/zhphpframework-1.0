<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
abstract  class model {
    /**
     * @param $sql
     * @param array $data
     * @return mixed
     * 直接执行sql
     */
    abstract public  function execute($sql,$data=array());
    abstract public  function query($sql,$data=array());
    abstract public function  queryAll($sql,$data=array());
    /**
     * @param $data
     * @return mixed
     * Av模型执行sql
     */
    abstract public  function add($tableName,$data);
    abstract public function  save($tableName,$data,$where='');
    abstract public function  delete($tableName,$where='');
    abstract public function  find($tableName,$field,$where='');
    abstract public  function findAll($tableName,$field,$where='');
}