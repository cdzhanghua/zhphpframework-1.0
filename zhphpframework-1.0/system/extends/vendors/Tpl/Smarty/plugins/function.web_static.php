<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-2-7
 * Time: 下午5:38
 */
function smarty_function_web_static($params, $template){
   return HTTP_URL.APP_NAME.'/web/html/';
}