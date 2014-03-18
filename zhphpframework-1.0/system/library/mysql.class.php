<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
class mysql {
     public static $db=null; #数据库对象
     protected  $conn=null; #链接资源类型
     private   $mysqlApi; #数据库链接接口标志
     private  $countPage=0; #分页总页码
     private  $p=1; #当前页码数
     public static $host;
     public static $user;
     public static $pwd;
     public static $dbName;
     public static $port;
     public static $charset;
     public static  $cachePath;

    public function  __construct(){
        $this->mysqlApi = extension_loaded('mysqli')?true:false;
        $this->mysqlApi === true?$this->mysqliConn():$this->mysqlConn();
     }
    public  function  __call($module,$args){
         echo  'error_log:'.__CLASS__.'中没有你请求的'.$module;
    }
    /**
     * @param $host
     * @param $user
     * @param $pwd
     * @param $dbName
     * @param int $port
     * @param string $charset
     *  mysql::dbConfig($host,$user,$pwd,$dbName,$port=3306,$charset='charset');
     */
    public static function  dbConfig($host,$user,$pwd,$dbName,$port=3306,$charset='utf8',$cachePath=''){
        self::$host=$host;
        self::$user=$user;
        self::$pwd=$pwd;
        self::$dbName=$dbName;
        self::$port=$port;
        self::$charset=$charset;
        self::$cachePath=$cachePath;#文件缓存地址
    }


    /**
     * @return mixed
     * mysql::init(); 获得数据操作对象
     * 单例模式实现数据库类初始化： 如果存在对象就返回mysql 对象否则就实例化对象
     */
    public static function init(){
        if( ! is_object(self::$db)){
            self::$db=new self();
            return self::$db;
        }
        return self::$db;
     }

    /**
     * @throws Exception
     * mysqli 链接方法
     */
    private  function  mysqliConn(){
       $this->conn=mysqli_connect(self::$host,self::$user,self::$pwd,self::$dbName,self::$port) or die('erro:数据库服务器链接失败!');
        mysqli_set_charset($this->conn,self::$charset);
     }
 /**
     * @throws Exception
     *  mysql 链接方法
     */
    private  function  mysqlConn(){
        $host=self::$host.':'.self::$port;
        $this->conn=mysql_connect($host,self::$user,self::$pwd) or die('erro:数据库服务器链接失败!');
        mysql_select_db(self::$dbName,$this->conn) or  die('erro:数据库不存在!');
        mysql_set_charset(self::$charset,$this->conn);
         unset($host);
      }
    /**
     * @param $str
     * @return string
     *  预防sql 注入
     */
    public  function quote($str){
        return $this->mysqlApi === true?mysqli_real_escape_string($this->conn,$str):mysql_real_escape_string($str,$this->conn);
     }

      /*============================================================================================================*/
    /**
     * @param $sql
     * @return bool|mysqli_result
     */
    private  function  mysqliExec($sql){
       return mysqli_query($this->conn,$sql);
   }
    /**
     * @param $sql
     * @return bool
     */
    private  function  mysqliAdd($sql){
           $result=mysqli_query($this->conn,$sql);
           unset($sql);
           return  empty($result)?false:true;
      }

    /**
     * @param $sql
     * @return bool
     */
    private function  mysqliSave($sql){
        $result=mysqli_query($this->conn,$sql);
        unset($sql);
        return  empty($result)?false:true;
    }

    /**
     * @param $sql
     * @return bool
     */
    private function  mysqliDel($sql){
        $result=mysqli_query($this->conn,$sql);
         unset($sql);
        return  empty($result)?false:true;
    }

    /**
     * @return int
     */
    public function  mysqliAffectedRows(){
         return mysqli_affected_rows($this->conn);
    }
    /**
     * @return int
     */
    private  function  mysqliLastId(){
        return mysqli_insert_id($this->conn);
    }

    /**
     * @param $tableName
     * @return array
     * 获取表结构
     */
    private  function mysqlidesc($tableName){
         $result=mysqli_query($this->conn,'desc '.$tableName);
         $dest=array();
         while($rows=mysqli_fetch_assoc($result)){
              $dest[]=$rows;
         }
          mysqli_free_result($result);
        unset($result);
        unset($tableName);
         return $dest;
   }

    /**
     * @param $sql
     * @return array
     *  查询一行数据
     */
    private function  mysqliQueryOne($sql){
       $result=mysqli_query($this->conn,$sql);
       $data=array();
       $data=mysqli_fetch_assoc($result);
       mysqli_free_result($result);
        unset($result);
        unset($sql);
       return $data;
   }

    /**
     * @param $sql
     * @return array
     */
    private function  mysqliQueryAll($sql){
        $result=mysqli_query($this->conn,$sql);
        $data=array();
        while($rows=mysqli_fetch_assoc($result)){
            $data[]=$rows;
        }
        mysqli_free_result($result);
        unset($result);
        unset($sql);
        return $data;
   }

    /**
     * @param $sql
     * @return int
     */
    private  function  mysqliQueryNum($sql){
       $result=mysqli_query($this->conn,$sql);
        unset($sql);
        return mysqli_num_rows($result);
   }
    private  function  mysqliCount($sql){
        $result=mysqli_query($this->conn,$sql);
        unset($sql);
        return  mysqli_num_rows($result);
    }

    /**
     * @return bool
     */
    private  function  mysqliClose(){
         mysqli_close($this->conn);
         return true;
     }

    /**
     * @param $tableName
     * @param int $pageNum
     * @param string $field
     * @param string $where
     * @return array
     */
    private  function  mysqliPage($tableName,$pageNum,$field,$where){
        $pageNum=empty($pageNum)?15:intval($pageNum); //每页显示5条
        $sql='select count(1) as sum from '.$tableName;
        $countNum=$this->mysqliQueryOne($sql);
        $countData=$countNum['sum'];
        $this->countPage=ceil($countData/$pageNum); //得到总页码数据
        $this->countPage=empty($this->countPage)?1:$this->countPage;
        $this->p=( ! empty($_GET['p']))?intval($_GET['p']):1;  //获取当前页码数

        $start=($this->p-1)*$pageNum; //开始游标
        $field=empty($field)?'*':strval($field);
        $where=empty($where)?null:strval($where);
        $sql='select '.$field.' from '.$tableName.' '.$where.' limit '.$start.','.$pageNum;
        $data=$this->mysqliQueryAll($sql);
        unset($countNum);
        unset($pageNum);
        unset($tableName);
        unset($countData);
        unset($sql);
        return  $data;
    }
    private  function  mysqliLimt($tableName,$pageNum,$field,$where,$iwhere){
        $pageNum=empty($pageNum)?15:intval($pageNum); //每页显示5条
        $sql='select count(*) as sum from '.$tableName.' '.$iwhere;
        $countNum=$this->mysqliQueryOne($sql);
        $countData=$countNum['sum'];
        $this->countPage=ceil($countData/$pageNum); //得到总页码数据
        $this->countPage=empty($this->countPage)?1:$this->countPage;
        $this->p=( ! empty($_GET['p']))?intval($_GET['p']):1;  //获取当前页码数
        $start=($this->p-1)*$pageNum; //开始游标
        $field=empty($field)?'*':strval($field);
        $where=empty($where)?null:strval($where);
        $sql='select '.$field.' from '.$tableName.' '.$where.' limit '.$start.','.$pageNum;
        $data=$this->mysqliQueryAll($sql);
        unset($countNum);
        unset($pageNum);
        unset($tableName);
        unset($countData);
        unset($sql);
        return  $data;
    }
    /*=======================================mysqli   结束===================================================================*/
    /**
     * @param $sql
     * @return resource
     */
    private  function mysqlExec($sql){
       return mysql_query($sql,$this->conn);
    }
    /**
     * @param $sql
     * @return bool
     */
    private function  mysqlAdd($sql){
           $result=mysql_query($sql,$this->conn);
           unset($sql);
           return  empty($result)?false:true;
      }
    /**
     * @param $sql
     * @return bool
     */
    private function  mysqlSave($sql){
        $result=mysql_query($sql,$this->conn);
        unset($sql);
        return  empty($result)?false:true;
    }

    /**
     * @param $sql
     * @return bool
     */
    private function  mysqlDel($sql){
        $result=mysql_query($sql,$this->conn);
        unset($sql);
        return  empty($result)?false:true;
    }

    /**
     * @return int
     */
    private function  mysqlAffectedRows(){
        return mysql_affected_rows($this->conn);
    }

    /**
     * @return int
     */
    private  function  mysqlLastId(){
         return mysql_insert_id($this->conn);
    }
    /**
     * @param $tableName
     * @return array
     * 获取表结构
     */
    private  function mysqldesc($tableName){
        $result=mysql_query('desc '.$tableName,$this->conn);
        $dest=array();
        while($rows=mysql_fetch_assoc($result)){
            $dest[]=$rows;
        }
        mysql_free_result($result);
        unset($result);
        unset($tableName);
        return $dest;
    }

    /**
     * @param $sql
     * @return array
     *  查询一行数据
     */
    private function  mysqlQueryOne($sql){
        $result=mysql_query($sql,$this->conn);
        $data=array();
        $data=mysql_fetch_assoc($result);
        mysql_free_result($result);
        unset($result);
        unset($sql);
        return $data;
    }
     private  function mysqlQueryAll($sql){
         $result=mysql_query($sql,$this->conn);
         $data=array();
         while($rows=mysql_fetch_assoc($result)){
             $data[]=$rows;
         }
         mysql_free_result($result);
         unset($result);
         unset($sql);
         return $data;
     }

    /**
     * @param $sql
     * @return int
     */
    private  function  mysqlQueryNum($sql){
         $result=mysql_query($sql,$this->conn);
         unset($sql);
        return   mysql_num_rows($result);
     }
    private  function  mysqlCount($sql){
        $result=mysqli_query($this->conn,$sql);
        unset($sql);
        return  mysqli_num_rows($result);
    }
    /**
     * @return bool
     */
    private  function  mysqlClose(){
        return mysql_close($this->conn);
    }

    /**
     * @param $tableName
     * @param int $pageNum
     * @param string $field
     * @param string $where
     * @return array
     */
    private  function  mysqlPage($tableName,$pageNum,$field,$where){
        $pageNum=empty($pageNum)?15:intval($pageNum); //每页显示5条
        $data=array();//分页的数据
        $sql='select count(1) as sum from '.$tableName;
        $countNum=$this->mysqlQueryOne($sql);
        $countData=$countNum['sum'];
        $this->countPage=ceil($countData/$pageNum); //得到总页码数据
        $this->countPage=empty($this->countPage)?1:$this->countPage;
        $this->p=( ! empty($_GET['p']))?intval($_GET['p']):1;  //获取当前页码数
        $start=($this->p-1)*$pageNum; //开始游标
        $field=empty($field)?'*':strval($field);
        $where=empty($where)?null:strval($where);
        $sql='select '.$field.' from '.$tableName.' '.$where.' limit '.$start.','.$pageNum;
        $data=$this->mysqliQueryAll($sql);
        unset($countNum);
        unset($pageNum);
        unset($tableName);
        unset($countData);
        unset($result);
        return  $data;
    }
    private  function  mysqlLimt($tableName,$pageNum,$field,$where,$iwhere){
        $pageNum=empty($pageNum)?15:intval($pageNum); //每页显示5条
        $data=array();//分页的数据
        $sql='select count(*) as sum from '.$tableName.' '.$iwhere;
        $countNum=$this->mysqlQueryOne($sql);
        $countData=$countNum['sum'];
        $this->countPage=ceil($countData/$pageNum); //得到总页码数据
        $this->countPage=empty($this->countPage)?1:$this->countPage;
        $this->p=( ! empty($_GET['p']))?intval($_GET['p']):1;  //获取当前页码数
        $start=($this->p-1)*$pageNum; //开始游标
        $field=empty($field)?'*':strval($field);
        $where=empty($where)?null:strval($where);
        $sql='select '.$field.' from '.$tableName.' '.$where.' limit '.$start.','.$pageNum;
        $data=$this->mysqliQueryAll($sql);
        unset($countNum);
        unset($pageNum);
        unset($tableName);
        unset($countData);
        unset($result);
        return  $data;
    }

    /*===========================================mysql 接口完=========================================================================*/
    /**
     * @param $sql
     * @return bool
     * 添加方法
     */
    public function  add($sql){
       return  $this->mysqlApi === true?$this->mysqliAdd($sql):$this->mysqlAdd($sql);
    }

    /**
     * @param $sql
     * @return bool
     * 修改方法
     */
    public function  save($sql){
       return  $this->mysqlApi === true?$this->mysqliSave($sql):$this->mysqlSave($sql);
    }

    /**
     * @param $sql
     * @return bool
     * 删除方法
     */
    public function del($sql){
      return  $this->mysqlApi === true?$this->mysqliDel($sql):$this->mysqlDel($sql);
    }

    /**
     * @param $sql
     * @return array
     * 查询一行数据
     */
    public function  queryOne($sql){
        return  $this->mysqlApi === true?$this->mysqliQueryOne($sql):$this->mysqlQueryOne($sql);
    }

    /**
     * @param $sql
     * @return array
     *  查询多行数据
     */
    public function queryAll($sql){
       return  $this->mysqlApi === true?$this->mysqliQueryAll($sql):$this->mysqlQueryAll($sql);
     }

    /**
     * @return array
     */
    public  function queryNum($sql){
        return $this->mysqlApi === true?$this->mysqliQueryNum($sql):$this->mysqlQueryNum($sql);
    }
    public function count($sql){
        return $this->mysqlApi ===true?$this->mysqliCount($sql):$this->mysqlCount($sql);
    }
    /**
     * @param $sql
     * @param bool $rows=true
     * @return int
     *  返回数据和统计行数
     */
    public function querycount($sql,$rows=true){
         if($rows){
             $data=$this->queryAll($sql);
             $data['count']=count($data);
             return $data;
         }else{
             return $this->queryNum($sql);
         }
    }

    /**
     * @param $sql
     * @return mixed
     *  返回资源结果集
     */
    public function exec($sql){
        return  $this->mysqlApi === true?$this->mysqliExec($sql):$this->mysqlExec($sql);
    }


    /**
     * @return bool
     *  取得上一次 insert update delete 操作所影响的行数
     */
    public function affectedRows(){
        return  $this->mysqlApi === true?$this->mysqliAffectedRows():$this->mysqlAffectedRows();
    }

    /**
     * @return int
     *  取得上一次插入成功的id
     */
    public function lastId(){
        return  $this->mysqlApi === true?$this->mysqliLastId():$this->mysqlLastId();
    }
    /**
     *  取得表结构
     */
    public function descTable($tableName){
        return  $this->mysqlApi === true?$this->mysqlidesc($tableName):$this->mysqldesc($tableName);
    }
    /**
     * 取得当前mysql接口类型
     */
    public function getMysqlApi(){
         return  $this->mysqlApi === true?'mysqli':'mysql';
    }

    /**
     * @return bool
     */
    public  function  closeDb(){
        return  $this->mysqlApi === true?$this->mysqliClose():$this->mysqlClose();
    }

    /**
     * 分页功能
     */
    public  function page($tableName,$pageNum,$field,$where){
        return  $this->mysqlApi === true?$this->mysqliPage($tableName,$pageNum,$field,$where):$this->mysqlPage($tableName,$pageNum,$field,$where);
    }
    public  function limt($tableName,$pageNum,$field,$where,$iwhere){
        return  $this->mysqlApi === true?$this->mysqliLimt($tableName,$pageNum,$field,$where,$iwhere):$this->mysqlLimt($tableName,$pageNum,$field,$where,$iwhere);
    }
    /**
     * 分页链接
     */
    public  function link($href){
        $pagelink=''; //分页链接字符串
         $p=$this->p;
        $countPage=$this->countPage;
        $pre=($this->p <= 1)?$p:$p-1; //上页
        $next=($p == $countPage)?$countPage:$p+1; // 下一页
//链接字符串  共  页  第 页   第一页   上一页  下一页  最后一页
        $pagelink='<p><span><a href="'.$href.'&p=1">第一页</a></span>';
        $pagelink.='<span><a href="'.$href.'&p='.$pre.'">上一页</a></span>';
        $pagelink.='<span><a href="'.$href.'&p='.$next.'">下一页</a></span>';
        $pagelink.='<span><a href="'.$href.'&p='.$countPage.'">最后一页</a></span>';
        $pagelink.='<span>第 '.$p.'页</span>/<span>共'.$countPage.' 页</span>';
        $pagelink.='</p>';
        unset($pre);
        unset($next);
        unset($countPage);
        unset($p);
        return $pagelink;
}
    public  function linkindex($href){
        $pagelink=''; //分页链接字符串
        $p=$this->p;
        $countPage=$this->countPage;
        $pre=($this->p <= 1)?$p:$p-1; //上页
        $next=($p == $countPage)?$countPage:$p+1; // 下一页
//链接字符串  共  页  第 页   第一页   上一页  下一页  最后一页
        $pagelink='<p><span><a href="'.$href.'?p=1.html">第一页</a></span>';
        $pagelink.='<span><a href="'.$href.'?p='.$pre.'.html">上一页</a></span>';
        $pagelink.='<span><a href="'.$href.'?p='.$next.'.html">下一页</a></span>';
        $pagelink.='<span><a href="'.$href.'?p='.$countPage.'.html">最后一页</a></span>';
        $pagelink.='<span>第 '.$p.'页</span>/<span>共'.$countPage.' 页</span>';
        $pagelink.='</p>';
        unset($pre);
        unset($next);
        unset($countPage);
        unset($p);
        return $pagelink;
    }
/**
 * 文件缓存查询
 */
     public function  queryFileCache($sql){
        if(is_dir(self::$cachePath)){
                 $fileName=self::$cachePath.'/'.md5($sql).'.txt';
                 if(file_exists($fileName)){
                     $serialize=file_get_contents($fileName);
                     $data=unserialize($serialize);
                     return $data;
                 }else{
                     $data=$this->queryAll($sql);
                     $serialize=serialize($data);
                     file_put_contents($fileName,$serialize);
                     return $data;
                 }
             }
      }

    /**
     * 自定义文件数据缓存目录
     */
     public  function fileCacheConfg($cachePath){
         self::$cachePath=$cachePath;#文件缓存地址
     }
}