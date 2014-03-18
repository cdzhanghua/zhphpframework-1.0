<?php
//分页类
class pagelist
{
    private $indexPage;//当前页
    //private $prevPage;
    //private $nextPage;
    private $totalNum;//总数据
    private $listNum;//显示条数
    private $pageNum;//总页数
    public $pagesize;//每页显示的内容数
    //private $tableName;
    //private $fieldarr;
    public $pageurl;//当前页的URL
    public $page;//当前页数值
    private $config=array('head'=>"条记录", "prev"=>"上一页", "next"=>"下一页", "first"=>"首页", "last"=>"末页");
    function __construct($totalnum = 0,$pagesize = 15, $listnum = 5){
        $this->pageSize = $pagesize;
        $this->listNum = $listnum;
        $this->totalNum = $totalnum;//总记录数
        //!empty($pagesize) && $this->pageSize=$pagesize;//每页显示条数
        //!empty($listNum) && $this->listNum=$listNum;//每页显示的页数

        if($this->totalNum > $this->pageSize && $this->totalNum > 0){

            $this->pageNum=ceil($this->totalNum / $this->pageSize);//总页数

        }else{
            $this->pageNum=1;
        }
    }


    //查询数据库获得当前页面的参数
    /*
    function setparam($tablename,$pagesize='',$listNum=''){
        $this->talbeName=$tablename;
        $totalLink=mysql_query('select count(*) from '.$tablename);
        $totalnum=mysql_fetch_row($totalLink);
        $this->totalNum=$totalnum[0];//总记录数
        !empty($pagesize) && $this->pageSize=$pagesize;//每页显示条数
        !empty($listNum) && $this->listNum=$listNum;//每页显示的页数

        if($this->totalNum > $this->pageSize){

            $this->pageNum=ceil($this->totalNum / $this->pageSize);//总页数

        }else{
            $this->pageNum=1;
        }
    }*/
    //获得当前页的url

    function geturl(){

        if(!empty($_SERVER["REQUEST_URI"]))
        {
            $nowurl = $_SERVER["REQUEST_URI"];
            $nowurls = explode("?",$nowurl);
            $this->pageurl = $nowurls[0];
        }
        else
        {
            $this->pageurl = $_SERVER["PHP_SELF"];//当前文件
        }

    }
    //获得当前页的页数值
    function getpage(){
        $page=$_GET['arcid'];
        if(empty($page) || ($page <= 0)) $page = 1;
        if($page > $this->pageNum) $page = $this->pageNum;
        $this->page = $page;
        return $page;

    }

    /*
    *设置URL信息，页数设置
    */

    function set($parm = array())
    {
        foreach($parm as $k=>$v)
        {
            $this->$k = $v;
        }
    }


    //获取当前页内容列表
    /*
        function getcontent()
        {
            $num=0;
            if($this->page > 0) $num = ($this->page - 1) * $this->pageSize;
            $sql='select * from '.$this->tablename.' LIMIT '.$num.' , '.$this->pageSize;
            $contentlist=mysql_query('select * from '.$this->talbeName.' LIMIT '.$num.' , '.$this->pageSize);
             while($content = mysql_fetch_assoc($contentlist))
             {

                $con[]=$content;

             }
             return $sql;
        }
        */

    /*获得分页列表
    *默认是以$_GET方式分页，其他方式需要自行设置，$parameter为辅助参数
    */
    function getlistpage($pid = '?pid=',$parameter = ''){
        $i=0;
        $str='';
        for($i = ($this->page - $this->listNum);$i <= ($this->page + $this->listNum);$i++)
        {

            if($i < 1 || $i > $this->pageNum) continue;
            if($i == $this->page) $str.='<a target="_self" href="#" style="color:#FF0000">'.$i.'</a>';
            else
                $str.='<a href="'.$this->pageurl.$pid.$i.$parameter.'">'.$i.'</a>';
        }
        return $str;
    }

    /*获得上一页,下一页,首页,末页,记录数
    *默认是以$_GET方式分页，其他方式需要自行设置，$parameter为辅助参数
    */

    function addcontent($pid = '?pid=',$parameter = ''){

        if($this->page > 1) {

            $pageid = $this->page - 1;
            $this->config['prev'] = '<a href="'.$this->pageurl.$pid.$pageid.$parameter.'">上一页</a>';

        }else{

            $this->config['prev']='';

        }

        if(($this->page >0 && $this->pageNum >1) && ($this->page <= $this->pageNum -1)){

            $pageid = $this->page + 1;
            $this->config['next']='<a href="'.$this->pageurl.$pid.$pageid.$parameter.'">下一页</a>';

        }else{

            $this->config['next']='';

        }


        if($this->pageNum >1){

            $this->config['last']='<a href="'.$this->pageurl.$pid.$this->pageNum.$parameter.'">末页</a>';
            $this->config['first']='<a href="'.$this->pageurl.$pid.$parameter.'">首页</a>';

        }else{

            $this->config['last']='';
            $this->config['first']='';

        }


        if($this->totalNum <= 0)
        {
            $this->config['head'] = '共有0条记录';
        }
        else
        {
            $this->config['head'] = '共有'.$this->totalNum.'条记录';
        }
        return $this->config;
    }
    //最终返回页
    /*function fpage()
    {


    }*/

}
?>