<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
/**
* 原理：请求分配token的时候，想办法分配一个唯一的token, base64( time + rand + action)
* 如果提交，将这个token记录，说明这个token以经使用，可以跟据它来避免重复提交。
*/
class token {
    /**
     * 得到当前所有的token
     *
     * @return array
     */
    public static function getTokens(){
        $key=config::readConfig('sesssion', 'key_token');
        $key=(!empty($key))?$key:null;
        $tokens=isset($_SESSION["{$key}"])?$_SESSION["{$key}"]:array();#解决session中出现undefined
        if (empty($tokens) && !is_array($tokens)){
            $tokens = array();
        }
		unset($key);
        return $tokens;
    }
    /**
     * 产生一个新的Token
     * @param string $formName
     * @param 加密密钥 $key
     * @return string
     */
    public static function granteToken($formName,$key =''){
        $key=empty($key)?config::readConfig('sesssion', 'key_token'):$key;
        $token = self::encrypt($formName.':'.session_id().':'.time(),$key);
		unset($key,$formName);
        return $token;
    }
    /**
     *  加密
     * @param type $str
     * @param type $key
     * @return type
     */
    public static function encrypt($str, $key=''){
        $key=  empty($key)?config::readConfig('sesssion', 'key_token'):$key;
        $coded = '';
        $keylength = strlen($key);
        $leng=strlen($str);
        for ($i = 0, $count = $leng; $i < $count; $i += $keylength) {
            $coded .= substr($str, $i, $keylength) ^ $key;
        }
		$newCode=str_replace('=', '', base64_encode($coded));
		unset($str,$key,$coded,$keylength,$leng);
		return $newCode;
    }
    /**
     * 解密
     * @param type $str
     * @param type $key
     * @return type
     */
    public static function decrypt($str, $key = ''){
        $key=  empty($key)?config::readConfig('sesssion', 'key_token'):$key;
        $coded = '';
        $keylength = strlen($key);
        $str = base64_decode($str);
        for ($i = 0, $count = strlen($str); $i < $count; $i += $keylength){
            $coded .= substr($str, $i, $keylength) ^ $key;
        }
		unset($str,$key,$keylength);
        return $coded;
    }
    
    /**
     * 删除token,实际是向session 的一个数组里加入一个元素，说明这个token以经使用过，以避免数据重复提交。
     *
     * @param string $token
     */
    public static function dropToken($token){
        $tokens = self::getTokens();
        $tokens[] = $token;
        $_SESSION[config::readConfig('sesssion', 'key_token')]=$tokens;
		unset($token,$tokens);
    }
    
    /**
     * 检查是否为指定的Token
     *
     * @param string $token    要检查的token值
     * @param string $formName
     * @param boolean $fromCheck 是否检查来路,如果为true,会判断token中附加的session_id是否和当前session_id一至.
     * @param string $key 加密密钥
     * @return boolean
     */
    
    public static function isToken($token,$formName,$fromCheck = false,$key =''){
         $tokens = self::getTokens();
         if (in_array($token,$tokens)){ //如果存在，说明是以使用过的token
		    unset($tokens);
            return false;
         }
		 $key=  empty($key)?config::readConfig('sesssion', 'key_token'):$key;
         $source = explode(':',self::decrypt($token,$key));
		 unset($token,$key);
         if($fromCheck){
            if($source[1] == session_id() && $source[0] == $formName){
			 unset($source,$formName);
             return true;
            }else{
			 unset($source,$formName);
             return false;
            }
         }else{
             if($source[0] == $formName){
			 unset($source,$formName);
             return true;
             }else{
                unset($source,$formName);
                return false;
             }
        }
     }
}