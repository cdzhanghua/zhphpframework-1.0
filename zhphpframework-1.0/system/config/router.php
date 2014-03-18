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
     * url关键词过滤
     */
    'filterWords'=>array('add ','and','count','order','table','by','create','delete','drop','from','grant','insert',
        'select','truncate','update','use','--','#','\'','"',';','group_concat','column_name','information_schema.columns',
        'table_schema','union','where','script'),
     /**
     *路由: 默认 modules='home'  controller='index'  action='index'
     */
    'parse'=>array(
        'm'=>'', #默认模块
        'c'=>'index', #默认控制器
        'a'=>'index', #默认动作
         #'url_Delimiter'=>'_', #url 分割符号
        'Static_suffix'=>'.html',
    ),
    'rest'=>array(
        '#([a-z]+)/([a-z]+).html#i'=>'upload/index',
    ),

    
);

