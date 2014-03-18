<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-3-17
 * Time: 上午10:49
 * To change this template use File | Settings | File Templates.
 */
function smarty_function_html_form($params, $template)
{
    #定义变量属性
    $action='';
    $method='post';
    $name='';
    $enctype='';

    #赋值
    foreach($params as $_key=>$_val){
        $$_key=$_val;
    }

    #变量属性判断
    if(isset($method) || empty($method)){
        $method='post';
    }
    $action=HTTP_URL.$action;

  #创建form hash
    $key=config::readConfig('session','key_token').substr($_SERVER['REQUEST_TIME'],0,-7);
    $formhash=token::encrypt($action,$key);
  //  httpRequest
    #组装form
     if( ! empty($enctype) ){
        $enctype='multipart/form-data';
        $form='<form action="'.$action.'" method="'.$method.'" name="'.$name.'" enctype="'.$enctype.'">
         <input type="hidden" name="formhash" value="'.$formhash.'" />';
    }else{
         $form='<form action="'.$action.'" method="'.$method.'" name="'.$name.'">
         <input type="hidden" name="formhash" value="'.$formhash.'" />';
     }
    return $form;
}