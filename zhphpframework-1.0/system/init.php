<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
######################web run start#################################################
header('Content-type: text/html; charset=utf-8');#设置头信息
header("cache-control: pulic,max-age=1800,must-revalidate");#cdn 加速缓存使用参数
date_default_timezone_set('PRC');#设置时间区域
$startTime=microtime(true);#开始计时
version_compare(PHP_VERSION, '5.2', '>=')?true:die('<lable style="color:#ff0000;">严重警告:php的版本低于php5.2!</lable>');#获取php版本并对php版本判断 Zhphp 框架最低ph版本为 php5.2
######################################## gzip compres start ############################################################
function CheckCanGzip(){#检测是否支持压缩
    $HTTP_ACCEPT_ENCODING=$GLOBALS['_SERVER']['HTTP_ACCEPT_ENCODING'];
    if(!ini_get('zlib.output_compression')){#判断你是否配置php服务器压缩
        if(extension_loaded("zlib")){
            if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false){
                return "x-gzip";
            }else if (strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false){
                return "gzip";
            }
        }
    }
    return 0;
}
/**
 * 页面压缩后输出
 * @param $content
 * @return string
 */
function ob_gzlib($content){
    $ENCODING = CheckCanGzip();
    if($ENCODING){
        $content = gzencode($content,6);
        header("Content-Encoding: ".$ENCODING);
        header("Vary: Accept-Encoding");
        header("Content-Length: ".strlen($content));
        return $content;
    }
    return false;
}
if(!ob_start("ob_gzlib")) ob_start();#打开磁盘缓冲
ob_implicit_flush(0);#提高缓冲执行效率
###################################  stystem set define start ##########################################################
defined('APP_DEBUG')?APP_DEBUG:define('APP_DEBUG',false);#是否设为调试
defined('DOC_ROOT')?DOC_ROOT:define('DOC_ROOT',$GLOBALS['_SERVER']['DOCUMENT_ROOT']);#得到服务器根目录地址
defined('ROOT_PATH')?ROOT_PATH:str_replace('\\','/',define('ROOT_PATH',dirname(dirname(__FILE__)).'/system/'));#得到当前的系统跟目录地址
defined('APP_NAME')?APP_NAME:define('APP_NAME','application');#默认工程名
defined('APP_PATH')?APP_PATH:define('APP_PATH',str_replace('\\','/',  dirname(dirname(__FILE__)).'/'.APP_NAME.'/'));#工程绝对路径
$http_type = ((isset($GLOBALS['_SERVER']['HTTPS']) && $GLOBALS['_SERVER']['HTTPS'] == 'on') || (isset($GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO']) && $GLOBALS['_SERVER']['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
$httpHost=isset($GLOBALS['_SERVER']['HTTP_HOST'])?$GLOBALS['_SERVER']['HTTP_HOST']:$_SERVER['HTTP_HOST'];
$requestUri=isset($GLOBALS['_SERVER']['REQUEST_URI'])?$GLOBALS['_SERVER']['REQUEST_URI']:$_SERVER['REQUEST_URI'];
defined('APP_URI')?APP_URI:define('APP_URI',$http_type.$httpHost.$requestUri);#当前文件的脚本文件地址
defined('SERVER_HOST')?SERVER_HOST:define('SERVER_HOST',$http_type.$httpHost.'/');#域名地址
defined('SERVER_NAME')?SERVER_NAME:define('SERVER_NAME',$http_type.$httpHost);#不带 / 的域名
defined('WEB_STATIC')?WEB_STATIC:define('WEB_STATIC',SERVER_HOST.APP_NAME.'/web/');#静态渲染层地址
defined('HTML_STATIC_PATH')?HTML_STATIC_PATH:define('HTML_STATIC_PATH', APP_PATH.'web/');#静态html地址
$project_work=dirname(APP_PATH);$arr=explode('/',$project_work);$project_name=  is_array($arr)?end($arr):array();#得到工程名
defined('PROJECT_PATH')?PROJECT_WORK:define('PROJECT_WORK',$project_work);#得到工程路径
defined('PROJECT_NAME')?PROJECT_NAME:define('PROJECT_NAME',$project_name);#得到工程名并设置全局常量
$httpurl=strrpos(DOC_ROOT,PROJECT_NAME) === false?SERVER_HOST.$project_name.'/':SERVER_HOST;#得到服务器路径地址
defined('HTTP_URL')?HTTP_URL:define('HTTP_URL',$httpurl);#设置全局常量
defined('APP_HOST')?APP_HOST:define('APP_HOST',$httpHost);
defined('UPLOAD_PATH')?UPLOAD_PATH:define('UPLOAD_PATH',APP_PATH.'uploads/');
defined('UPLOAD_URL')?UPLOAD_URL:define('UPLOAD_URL',HTTP_URL.APP_NAME.'/uploads/');
unset($http_type,$is_version,$httpHost,$requestUri,$project_work,$project_name,$httpurl,$arr);#手动销毁相应的变量
######################################## php.ini config ################################################################
#程序对服务器的安全配置
ini_set('safe_mode','On');#打开php安全模式
ini_set('expose_php','Off');#对外隐藏php版本信息
ini_set('log_errors','On');#打开错误日志功能
ini_set('error_log',APP_PATH.'data/error_log/general_errors.log');#记录错误日志文件
ini_set('register_globals','Off');#关闭全局变量注册
ini_set('memory_limit','512M');#设置运行内存
APP_DEBUG == false?error_reporting(0):error_reporting(E_ALL);#设置错误显示级别
if( !session_id() ) { session_start(); }else{ session_start(); }#session 开始工作
###################################### framework engine start ##########################################################
include_once ROOT_PATH.'engine/engine.class.php'; #加载核心类
engine::loadCommon('common');#加载系统工具函数文件
config::loadConfig();#加载配置
set_error_handler('error');#自定义错误函数
#防sql注入与xss攻击验证
$_REQUEST = zh_input_bb($_REQUEST);
$_GET = zh_input_bb($_GET);
$_POST = zh_input_bb($_POST);
$_COOKIE = zh_input_bb($_COOKIE);
$_SESSION= zh_input_bb($_SESSION);
$router=engine::load('router');#路由引擎
engine::loadComponent();#控制器加载组件文件
dispatcher::dispatch($router);#路由分发
$endTime=microtime(true);
$time=$endTime-$startTime;
if(APP_DEBUG){
    $runtime = memory_get_usage();
   die('<div style="color:green; width:800px; height:40px; text-align:center;">调度总时间:'.$time.'seconds---------运行占用内存:(单位：MB):'.sprintf('%01.2f',($runtime) / 1024 / 1024).'MB'.'</div>');
}
ob_end_flush();
die();
###########################网页运行结束!(以下内容为非法内容)############################################################