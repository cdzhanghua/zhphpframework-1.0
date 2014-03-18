<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-6
 * Time: 下午8:14
 */
function smarty_function_Zhdebug($params, $template)
{
    if(APP_DEBUG){
	set_time_limit(0);
        $include_files = get_included_files();
        $include_file = '';
        foreach ($include_files as $file) {
            $include_file .= basename($file) . '<br/>';
        }
		$runtime = memory_get_usage();
	    $str = '<div style="border:1px solid #E6941A;margin:25px;padding:10px ">如下信息为当前系统运营信息：<br />';
        /*engine::load_func('bom','instance',DOC_ROOT);#系统自动捕获错误,去除bom字符*/
		$str.= '运行内存:(单位：MB):<span  style="color:#0E7B39;">' .sprintf('%01.2f',($runtime) / 1024 / 1024).'MB'. '</span><br />';
        $str .= '当前所有加载文件名称<pre>';
        $str .= $include_file.'</pre>';
        $str.='
    <h3> A:面向对象开发模式 oop</h3>
    <h3> B:统一入口 index.php</h3>
    <h3> C:程序运行之前，安全过滤非法</h3>
    <h3> D:基于配置开发，灵活、简单、稳定</h3>
    <h3> E:程序运行效率快，占用内存少</h3>
    <h3> F:路由自动支持: get模式、pathinfo模式，已经兼容模式</h3>
    <h3> G:程序支持url伪静态，方便seo</h3>
    <h3> H:程序支持Smarty模板技术</h3>
    <h3> I:程序支持mysql、mysqli pdo接口操作，程序自动选择最优接口操作数据</h3>
    <h3> J:程序支持页面缓存，浏览器缓存、数据缓存技术，加快页面访问效率</h3>
    <h3> K:程序支持服务器端程序压缩，浏览器端解压技术,加快页面执行效率</h3>
    <h3> L:程序自动捕获错误,减少程序漏洞</h3>
    <h3> M:依据配置在上线前，可以自动去除Bom 错误,官方建议上线后请关闭该功能，以减少内存的消耗。</h3>
    <h3> N:性能测试数据报告:D:\Apache2.2\bin>ab.exe -c 10 -n 1000 http://127.0.0.1/index.php/</h3>
    <h3> O:session分级存储,提高性能</h3>
    <h3> P:支持页面压缩技术</h3>
    <h3> Q:支持第三方插件应用</h3>
    <h3> R:支持第三方插件应用</h3>
    <h3> S:widget视图小挂机应用,代码重用</h3>
    <h3> U:layout视图模板布局应用,支持多模板继承</h3>
    <h3> V:编写zhphp框架的目的在于学习mvc,学习oop,并逐步理解mvc的耦合性能、代码重用</h3>
    <h3> W:请关注,感谢你的应用,zhphp是入门级框架的最好的案例</h3>';
    $str .='</div>';
    unset($include_files,$include_file,$runtime);
    return  $str;
    }
}