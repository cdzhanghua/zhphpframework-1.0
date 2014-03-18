<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-2-4
 * Time: 下午4:39
 * To change this template use File | Settings | File Templates.
 */
/**
 * @param $params  参数列表
 * @param $content  内容  $content是使用标签时，标签之间的循环内容
 * @param $template $smarty 模板对象
 * @param $repeat
 */
function smarty_block_widget($params, $content, $template, &$repeat)
{
   if(is_null($content)){
       return null;
   }
    $name=null;
    $control=null;
    $args=array();
    foreach($params as $_key=>$_val){
        $$_key=$_val;
    }
    $fileName=APP_PATH.'widget/'.$name.'.php';
   return include_once($fileName);
}