<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
class script {
   /**
     * 封装js函数
     */
    public static function write($command){
        echo '<script>'.$command.'</script>';
    }
    /**
     * 弹出框
     */
    public static function alter($msg){
        self::write('alert("'.$msg.'")');
    }
    /**
     * 前进或者后退
     */
    public static function backOrGo($pageNumber){
        $pageNumber=empty($pageNumber)?'0':$pageNumber;
        if($pageNumber == '0'){
            self::reload();
        }else{
            self::write('window.history.go('.$pageNumber.')') ;
        }
    }

    /**
     * 页面刷新
     */
    public static function reload(){
        self::write('window.location.reload()');
    }
    public static function freload(){
        self::write('parent.location.reload()');
    }
    /**
     * 页面转向
     */
    public static function location($file){
        if(empty($file)){
            self::reload();
        }else{
            self::write('window.location.href="'.$file.'"');
        }
    }
 }