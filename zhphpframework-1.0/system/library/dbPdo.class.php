<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class dbPdo extends model {
  /**
     * @param $sql
     * @param array $data
     * @return mixed
     * 直接执行sql,执行非查询sql insert/updata/delete
     */
    public function execute($sql, $data = array())
 {
        // TODO: Implement execute() method.
        if(empty ($data)){
            $dbh=$this->prepare();#得到数据库操作对象
            $count=$dbh->exec($sql);#快速执行sql
            unset($sql);
            return $count;
        }else{
           $sth=$this->prepare($sql);#预处理sql语句，返回预处理对象
           $sth=$this->bindParam($sth, $data);#绑定数据
           $count=$sth->execute();#执行sql
           $sth->closeCursor();
           unset ($sql,$data);
           return $count;
        }
    }
   /**
    * 查询一行数据
    * @param type $sql
    * @param type $data
    * @return type 
    */
    public function query($sql, $data = array())
    {
        // TODO: Implement query() method.
      
        if(empty ($data)){
               $dbh=$this->prepare();#得到数据库操作对象
               $sth = $dbh->query($sql);#执行查询sql语句
               if(is_object($sth)){
                $result = $sth->setFetchMode(PDO::FETCH_ASSOC);#设置数据返回格式为关联数组
                $uData=$sth->fetch();
                $sth->closeCursor();
                unset ($sql,$data,$result);
                return $uData;
               }else{
                   return false;
               }
           }else{
              $sth=$this->prepare($sql);#预处理sql语句，返回预处理对象
              $sth=$this->bindParam($sth, $data);#绑定数据
              $count=$sth->execute();#执行sql
              $result = $sth->setFetchMode(PDO::FETCH_ASSOC);#设置数据返回格式为关联数组
              $uData=$sth->fetch();
              $sth->closeCursor();
              unset ($sql,$data,$result);
              return $uData;
          }
       }
   /**
    *查询所有
    * @param type $sql
    * @param type $data
    * @return type 
    */
    public function  queryAll($sql, $data = array())
    {
        // TODO: Implement queryAll() method.
         if(empty ($data)){
                $dbh=$this->prepare();#得到数据库操作对象
                $sth = $dbh->query($sql);#执行查询sql语句
                if(is_object($sth)){
                  $result = $sth->setFetchMode(PDO::FETCH_ASSOC);#设置数据返回格式为关联数组
                  $uData=$sth->fetchAll();
                   $sth->closeCursor();
                   unset ($sql,$data,$result);
                   return $uData;  
                }else{
                    return false;
                }
           }else{
              $sth=$this->prepare($sql);#预处理sql语句，返回预处理对象
              $sth=$this->bindParam($sth, $data);#绑定数据
              $count=$sth->execute();#执行sql
              $result = $sth->setFetchMode(PDO::FETCH_ASSOC);#设置数据返回格式为关联数组
              $uData=$sth->fetchAll();
              $sth->closeCursor();
              unset ($sql,$data,$result);
              return $uData;
          }
   }

    /**
     *  查询统计
     * @param $sql
     * @param array $data
     * @return mixed
     */
    public function total($sql, $data = array()){
        if(empty ($data)){
            $dbh=$this->prepare();#得到数据库操作对象
            $sth = $dbh->query($sql);#执行查询sql语句
            $count=$sth->columnCount();#得到查询的结果行数
            $sth->closeCursor();#关闭游标，使语句能再次被执行
            unset ($sql,$data);
            return $count;
        }else{
            $sth=$this->prepare($sql);#预处理sql语句，返回预处理对象
            $sth=$this->bindParam($sth, $data);#绑定数据
            $sth->execute();#执行sql
            $count=$sth->fetchColumn();#得到查询的结果行数
            $sth->closeCursor();#关闭游标，使语句能再次被执行
            unset ($sql,$data);
            return $count;
        }
    }



    /**
     * @param $data
     * @return mixed
     * Av模型执行sql
     */
    public function add($tableName,$data)
    {
      // TODO: Implement add() method.
        $queryStr=$this->setQuery($data);#得到预处理查询格式
        $tableName=strtoupper($this->getTab_().$tableName);#得到表名
        $sql='INSERT  INTO  '.$tableName.'  VALUES  ( '.$queryStr.')';#组装sql语句
        $rows=$this->execute($sql, $data);#执行sql
        unset($queryStr,$tableName,$sql,$data);
        return $rows;
    }
  /**
   * 修改
   * @param type $tableName
   * @param type $data
   * @param type $where
   * @return type 
   */
    public function  save($tableName,$data,$where='')
    {
        // TODO: Implement save() method.
          $queryStr=$this->setQuery($data);#得到预处理查询格式
          $tableName=$this->getTab_().$tableName;#得到表名
          if(empty ($where)){ #如果不存在where 那么where 条件就是data里面的最后一个元素
              $arr=explode(',', $queryStr);
               $tt=end($arr);
               $where='  WHERE   '.$tt;
          }else{ #否则where 要么是数组要么是字符串
              if(is_string($where)){
                  $where='  WHERE   '.$this->where($where);
              }else if(is_array($where)){
                  $where='  WHERE   '.$this->setQuery($data);#得到预处理查询格式
              }
               
          }
          $sql='UPDATE '.$tableName.' SET '.$queryStr.$where;
          $rows=$this->execute($sql, $data);#执行sql
          unset($queryStr,$tableName,$sql,$data,$where);
          return $rows;
    }
  /**
   * 删除
   * @param type $tableName
   * @param string $where
   * @return type 
   */
    public function  delete($tableName,$where='')
    {
        // TODO: Implement delete() method.
           $tableName=$this->getTab_().$tableName;#得到表名
           if(is_string($where)){#如果是字符串
                $where=empty($where)?null: '  WHERE   '.$this->where($where);
                 $sql='DELETE FROM '.$tableName .$where;
                 $rows=$this->execute($sql);#执行sql
                  unset($tableName,$sql,$where);
                  return $rows;
              }else if(is_array($where)){#如果是数组
                  $queryWhere='  WHERE   '.$this->setQuery($where);#得到预处理查询格式
                  $sql='DELETE FROM '.$tableName .$queryWhere;
                   $rows=$this->execute($sql,$where);#执行sql
                  unset($tableName,$sql,$where);
                  return $rows;
              }
         
         
    }
  /**
   * 查找一行
   * @param type $tableName
   * @param type $field
   * @param string $where
   * @return type 
   */
    public function  find($tableName,$field, $where='')
    {
        // TODO: Implement find() method.
          $tableName=$this->getTab_().$tableName;#得到表名
           if(is_string($where)){#如果是字符串
               $where=empty($where)?null: '  WHERE   '.$this->where($where);
               $sql='SELECT  '.$field.'  FROM  '.$tableName.$where;
               echo $sql;
                $udata=$this->query($sql);
                unset($tableName,$where,$sql);
                return $udata;
           }else if(is_array($where)){
                $queryWhere='  WHERE   '.$this->setQuery($where);#得到预处理查询格式
                $sql='SELECT  '.$field.'  FROM  '.$tableName.$queryWhere;
                $udata=$this->query($sql,$where);
                unset($tableName,$where,$sql);
                return $udata;
           }
    }
   /**
    * 查找多行
    * @param type $tableName
    * @param type $data
    * @param string $where
    * @return type 
    */
    public function findAll($tableName,$field, $where='')
    {
        // TODO: Implement findAll() method.
        $tableName=strtoupper($this->getTab_().$tableName);#得到表名
           if(is_string($where)){#如果是字符串
               $where=empty($where)?null: '  WHERE   '.$this->where($where);
               $sql='SELECT  '.$field.'  FROM  '.$tableName.$where;
               $udata=$this->queryAll($sql);
                unset($tableName,$where,$sql);
                return $udata;
           }else if(is_array($where)){
                $queryWhere='  WHERE   '.$this->setQuery($where);#得到预处理查询格式
                $sql='SELECT  '.$field.'  FROM  '.$tableName.$queryWhere;
                $udata=$this->queryAll($sql,$where);
                unset($tableName,$where,$sql);
                return $udata;
           }
        
    }
    private function  where($where){
        $dbh=$this->prepare();#得到数据库操作对象
        $where=gjj($where);
        $dbh->quote($where);#sql条件进行预防sql注入处理
        return $where;
    }

    /**
     *预处理绑定
     * @param type $sth
     * @param type $data
     * @return type 
     */
    private function bindParam($sth,$data){
        $keys=array_keys($data);
        $count=count($data);
        if(is_int($keys[0])){
           for($i=0;$i<$count;$i++){
             $sth->bindParam($i+1,$data[$i]);
          }  
        }else{
            for($i=0;$i<$count;$i++){
             $sth->bindParam(':'.$keys[$i],$data[$keys[$i]]);
          } 
        }
        unset($keys,$count,$data);
        return $sth;
    }
    
   /**
     *预处理sql，返回预处理对象
     * @param type $sql
     * @return type 
     */
    private function  prepare($sql=''){
        $dbh=config::readConfig('DbConnection');#得到pdo操作对象
        #$dbh->setAttribute(PDO::ATTR_CASE,PDO::CASE_UPPER);#强制大写#
        if($sql == ''){
           return  $dbh;
        }else{
           $sth=$dbh->prepare($sql);#预处理sql
           return $sth;  
        }
      }
    /**
     *得到表前缀
     * @return type 
     */
    public function  getTab_(){
       $db=config::readConfig('db');
       if(isset($db['table_pre'])){
           return $db['table_pre'];
       }else{
           return null;
       }
    }
    /**
     *设置查询预处理sql格式
     * @param type $data
     * @return type 
     */
    private function setQuery($data){
        $queryStr='';
        $key=key($data);
        $count=count($data);
        if(is_int($key)){
            for($i=0;$i<$count;$i++){
               $queryStr.='?,'; 
            }
        }else{
              $keys=array_keys($data);
              for($i=0;$i<$count;$i++){
                   $queryStr.=$keys[$i].'=:'.$keys[$i].',';  
              }
              unset($keys);
         }
        $queryStr=substr($queryStr, 0, -1);
        unset($key,$count,$data);
        return $queryStr;
    }
}
