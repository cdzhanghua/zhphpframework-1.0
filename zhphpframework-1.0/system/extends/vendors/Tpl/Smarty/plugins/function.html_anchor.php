<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-2-4
 * Time: 下午4:59
 * To change this template use File | Settings | File Templates.
 */
function smarty_function_html_anchor($params, $template){

    $href=null;
    $id=null;
    $class=null;
    $name=null;
    $target=null;
    $text=null;
    $m=null;
    $c=null;
    $a=null;
    $args=null;
    $get='';
    $type='pathinfo';
    foreach($params as $_key=>$_val){
        $$_key=$_val;
    }
    $target=empty($target)?'_self':$target;
    $attr='id="'.$id.'" class="'.$class.'" name="'.$name.'"  target="'.$target.'"';
    if($m != null || $c != null || $a != null){
         $m=  empty($m)?'':'m='.$m.'&';
         $c= empty($c)?'':'c='.$c.'&';
         $a= empty($a)?'':'a='.$a.'&';
         $uri=SERVER_HOST.'index.php?'.$m.$c.$a;#得到路由
         if(isset($args)){#解析参数
             $vars=($args == '')?'':explode(',', $args);#解析参数
             if(is_array($vars)){
                foreach($vars as $key=>$_val){
                      $arr=explode(':',$_val);
                     $get.='&'.$arr[0].'='.$arr[1];
                 } 
             }
         }
         $anthor= '<a href="'.$uri.$get.'" '.$attr.'>'.$text.'</a>';
  }else {
         if(isset($args)){#get方式
            $routers=explode('/', $href);#得到路由
            $count=count($routers);   #判断个数
            if($count == 3){
                 $m=$routers[0];
                 $c=$routers[1];
                 $a=$routers[2];
               $uri=SERVER_HOST.'index.php?m='.$m.'&c='.$c.'&a='.$a;
             }else if($count == 2){
                 $c=$routers[0];
                 $a=$routers[1];
                $uri=SERVER_HOST.'index.php?c='.$c.'&a='.$a;
             }
            $vars=($args == '')?'':explode(',', $args);#解析参数
             if(is_array($vars)){
                foreach($vars as $key=>$_val){
                      $arr=explode(':',$_val);
                     $get.='&'.$arr[0].'='.$arr[1];
                 } 
             }
             $anthor= '<a href="'.$uri.$get.'" '.$attr.'>'.$text.'</a>';
          }else{#pathInfo
              $anthor= '<a href="'.SERVER_HOST.$href.'" '.$attr.'>'.$text.'</a>';
         }
   }
return $anthor;

}