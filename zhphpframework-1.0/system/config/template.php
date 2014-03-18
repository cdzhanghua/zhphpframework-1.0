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
   'tpl'=>array(
        'engine'=>'Smarty',
        'compile_check'=>true,#是否都需要重新编译  开发请设置true, 上线是请设置为 false'
        'caching'=>false,#是否缓存 #开发请部署为false，上线时请部署为 true
        'cache_lifetime'=>-1,#缓存的过期时间  单位： 秒 -为永不过期
        'left_delimiter'=>'<{',#左边界符号
        'right_delimiter'=>'}>',#右边界符号
        'cache_dir'=>'',#配置你的缓存目录 #如果为空就是默认
         'compile_dir'=>'',#配置你的编译目录 #如果为空就默认
        'template_dir'=>'',#配置你的模板目录 #如果为空就默认
    ),#支持多个模版引擎
 );

