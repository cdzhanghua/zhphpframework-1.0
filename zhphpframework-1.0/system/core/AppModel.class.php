 <?php
 /**
  * Created by JetBrains PhpStorm.
  * User: 张华
  * Date: 14-3-8
  * Time: 下午12:21
  * QQ: 746502560@qq.com
  * To change this template use File | Settings | File Templates.
  */
 class AppModel extends model implements cache,  nosql{
      /**
      * @param $sql
      * @param array $data
      * @return mixed
      * 直接执行sql
      */
     public function execute($sql, $data = array())
     {
         // TODO: Implement execute() method.
         $this->db->execute($sql, $data = array());
     }

     public function query($sql, $data = array())
     {
         // TODO: Implement query() method.
         $this->db->query($sql, $data = array());
     }

     public function  queryAll($sql, $data = array())
     {
         // TODO: Implement queryAll() method.
         $this->db->queryAll($sql, $data = array());
     }

     /**
      * @param $data
      * @return mixed
      * Av模型执行sql
      */
     public function add($tableName, $data)
     {
         // TODO: Implement add() method.
         $this->db->add($tableName, $data);
     }

     public function  save($tableName, $data, $where = '')
     {
         // TODO: Implement save() method.
         $this->db->save($tableName, $data, $where = '');
     }

     public function  delete($tableName, $where = '')
     {
         // TODO: Implement delete() method.
         $this->db->delete($tableName, $where = '');
     }

     public function  find($tableName, $field, $where = '')
     {
         // TODO: Implement find() method.
         $this->db->find($tableName, $field, $where = '');
     }

     public function findAll($tableName, $field, $where = '')
     {
         // TODO: Implement findAll() method.
         $this->db->findAll($tableName, $field, $where = '');
     }
     ##################################################################################################################
     protected $db;
     public function __construct(){
         $this->db=$this->database();
     }
     /**
      *手动获取数据库操作对象
      * @return type
      */
     protected function database(){
         return  database::init();
     }
     /**
      * 设置属性
      * @param $attribute
      */
     public   function setAttribute($attribute){}

     }
