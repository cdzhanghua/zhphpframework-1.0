<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function smarty_function_url($params, $template){
   $href=null;
   $args=null;
    $get='';
    #解析得到参数
   foreach($params as $_key=>$_val){
        $$_key=$_val;
    }
  if(isset($args)){#get方式
            $routers=explode('/', $href);#得到路由
            $count=count($routers);   #判断个数
            if($count == 3){
                 $m=$routers[0];
                 $c=$routers[1];
                 $a=$routers[2];
               $uri=HTTP_URL.'index.php?m='.$m.'&c='.$c.'&a='.$a;
             }else if($count == 2){
                 $c=$routers[0];
                 $a=$routers[1];
                $uri=HTTP_URL.'index.php?c='.$c.'&a='.$a;
             }
            $vars=($args == '')?'':explode(',', $args);#解析参数
             if(is_array($vars)){
                foreach($vars as $key=>$_val){
                      $arr=explode(':',$_val);
                     $get.='&'.$arr[0].'='.$arr[1];
                 } 
             }
            return $uri.$get;
          }else{#pathInfo
           return HTTP_URL.$href;
         }
    
}