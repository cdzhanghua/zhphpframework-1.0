<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class Demo {
 public  static function controllDemo(){
$script = <<<demo
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class IndexController extends AppController{
    public function indexAction() {
       \$this->render();
    }
     public function testAction() {
       echo 'hi! test';
    } 
}      
demo;
    file_put_contents(APP_PATH.'controllers'. '/IndexController.php', $script);

$script = <<<demo
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
class AdminController extends AppController{
    public function indexAction() {
       echo 'admin';
    }
}
demo;
     file_put_contents(APP_PATH.'modules/admin/controllers'. '/AdminController.php', $script);

$script = <<<demo
<!DOCTYPE html>
<html>
<head>
     <{block name='charset'}><meta charset="utf-8"><{/block}>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="copyright" content="http://www.zhphp.net" />
    <meta http-equiv="Contest-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="robots" content="xxxx,yyy" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,minimum-scale=1.0,maximum-scale=1.0">
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="no" />
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
    <title><{block name='title'}>我是标题 <{/block}></title>
      <link  rel="stylesheet" type='text/css' href='<{web_themes}>css/rest.css' />
     <link  rel="stylesheet" type='text/css' href='<{web_themes}>css/froms.css' />
     <link  rel="stylesheet" type='text/css' href='<{web_themes}>css/tools.css' />
    <!--[if lt IE 9]>
       <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <{block name='style'}> <{\$Smarty.block.child}>  <{/block}>
</head>
<body>
<div class="wrap">
  <{block name='header'}>
      <div class='header'>
          <h1>zhphp框架</h1>
          <p>welcome to zhphp-1.0       2014-2-6</p>
      </div>
 <{/block}>
 <{block name='main'}>
  <div class='main'>
      <!--这里就是子模板的内容 \$Smarty.block.child 将子模板的内容添加到父模板的位置中-->
     <{\$Smarty.block.child}>
  </div>
<{/block}>
<{block name='footer'}>
  <div class='footer'>版权所有@遵循BSD开源协议</div>
<{/block}>
</div>
  <{block name='js'}> <{\$Smarty.block.child}> <{/block}>
</body>
</html>
demo;
     file_put_contents(APP_PATH.'views/layout/main1.html', $script);

$script = <<<demo
<{extends file="layout/main1.html"}>
     <{block name='title'}>zhphp 欢迎你<{/block}>
<{block name="main"}>
 <{Zhdebug}>
<{/block}>
demo;
     file_put_contents(APP_PATH.'views/Index/index.html', $script);
  unset($script);
 }
}