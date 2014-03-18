<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class html {
    /**
     * arr2option
     * 数组转成<option></option>列表
     * @param array $arr 待转换的数组
     * @param string $value option选项中的value所对应的数组中的key
     * @param string $name option选项中<option>与</option>之间用于描述的文字对应数组的key
     * @param string $selected 与value对比，相同的值则设置为选中状态
     * @return string 返回html代码字符串
     */
  public  static  function arr2option($arr,$value,$name,$selected=""){
    $option="";
    foreach($arr as $v){
        if(!is_array($v)) continue;
        if(!isset($v[$value]) || !isset($v[$name])) continue;
        if($v[$value]==$selected){
            $option.="<option value=\"{$v[$value]}\" selected>{$v[$name]}</option>".PHP_EOL;
        }else{
            $option.="<option value=\"{$v[$value]}\">{$v[$name]}</option>".PHP_EOL;
        }
    }
    return $option;
}
}