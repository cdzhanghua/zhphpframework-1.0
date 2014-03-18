<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
return array(
    /**
     * 开发环境配置
     */
    'develop'=>array(
        #'checkControll'=>true, #是否严格checkControll路由,如果开启所有的模块、控制器就必须注册,否则程序就错误
      ),
    /**
     * 应用程序配置
     */
   'app'=>array(
       'charset'=>'utf-8',#网站编码
       'layout'=>'main1',#布局主体页面
       'autoCheckToken'=>true,#是否强制自动验证token,
       'upload_path'=>'',#上传文件路径
       'admin_module'=>'admin',#网站后台模块名称
       'title'=>'欢迎使用 zhphp 框架1.0版本',
       'open_basedir'=>'',#允许用户可操作的文件某目录(配置open_basedir 的时候一定要注意)
     ),
    /**
     *设置所允许上传的文件类型
     */
     'uplodmimes'=> array(
        'gif'	=>	'image/gif',
        'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
        'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
        'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
        'png'	=>	array('image/png',  'image/x-png'),
        'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
        'bit2'  =>  'application/octet-stream',//任意二进制文件
   ),
   
    /**
     * session 配置
     */
    'sesssion'=>array(
        'key_token'=>'zhphpframework',#session加密方式
        'save_handler'=>'file',#session保存方式
        'save_path'=>'data/session/',#session保存路径
        'auto_start'=>0,#是否自动打开session
        'maxlifetime'=>1440,#session生命周期
    ),
    /**
     * 自动加载你的components
     */
    /*'components'=>array(
      'controllers/components'=>array('login','user'),
      'modules/home/controllers/components'=>array('utils'),
  ),*/
    /**
     *模块注册
     */
    'modules'=>array('home','admin','member','shoping','oa','crm','cms'),
    /**
     * 控制器注册:规则
     * 1.如果controller是数字索引数组,value就是控制文件真实名
     *  比如： 'controller'=>array( 'index','post','page','test'),
     *  url构造:  http://127.0.0.1/index.php?m=module&c=index&a=action&id=123
     *            http://127.0.0.1/module/index/action/123
     *  说明: index指向IndexControll.php
     *  
     *  key=>value: 如果key是关联索引数组,那么key是真实控制器文件名,value是变异文件名
     *  'controller'=>array( 'index'=>'idex','post'=>'pt','page'=>'pg','test'=>'tt'),  
     * url构造:  http://127.0.0.1/index.php?m=module&c=idex&a=action&id=123
     *            http://127.0.0.1/module/idex/action/123
     *  说明: 路由中的 idex 将映射到真实的 IndexControll.php
     * 
     *  允许混合使用
     * 
     */
    'controller'=>array( 'index','post'=>'pt','page','test'),

 );

