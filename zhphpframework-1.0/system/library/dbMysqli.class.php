<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class dbMysqli  extends model {
    //put your code here
    /**
     * @param $sql
     * @param array $data
     * @return mixed
     * 直接执行sql
     */
    public function execute($sql, $data = array())
    {
        // TODO: Implement execute() method.
    }

    public function query($sql, $data = array())
    {
        // TODO: Implement query() method.
    }

    public function  queryAll($sql, $data = array())
    {
        // TODO: Implement queryAll() method.
    }

    /**
     * @param $data
     * @return mixed
     * Av模型执行sql
     */
    public function add($tableName, $data)
    {
        // TODO: Implement add() method.
    }

    public function  save($tableName, $data, $where = '')
    {
        // TODO: Implement save() method.
    }

    public function  delete($tableName, $where = '')
    {
        // TODO: Implement delete() method.
    }

    public function  find($tableName, $field, $where = '')
    {
        // TODO: Implement find() method.
    }

    public function findAll($tableName, $field, $where = '')
    {
        // TODO: Implement findAll() method.
    }
}
