<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
/**
 * 获取客户端的ip
 * @return bool
 */
function  get_ip(){
    $ip=false;
    if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
        for ($i = 0; $i < count($ips); $i++) {
            if (!preg_match("^(10|172\.16|192\.168)\.", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
/**
 * get方式url 参数过滤
 *  $android_Id = 'http://www.test.com/doc?android=1&imcp_topic=json&aid=46571338&aab=bb&gv=4.0.2&df=androidphone';
 *  $paramArg = array('android'=>true,'imcp_topic'=>true,'aid'=>true);
 *  $filterUrl = common::filterParam($android_Id,$paramArg);
 *  应用后返回结果:http://www.test.com/doc?android=1&imcp_topic=json&aid=46571338
 * 后面的就自动过滤掉&aab=bb&gv=4.0.2&df=androidphone';
 * @param type $url
 * @param type $paramArgs
 * @return string|boolean
 */
function filterParam($url,$paramArgs){
    $parts = parse_url($url);
    if(!isset($parts['query'])){ return false; }
    parse_str($parts['query'], $output);
    $path = array_intersect_key($output,$paramArgs);
    $pathStr = http_build_query($path);
    $parts['query'] = $pathStr;
    $filterUrl = $parts['scheme'].'://'.$parts['host'].$parts['path'].'?'.$parts['query'];
    unset($parts,$path,$pathStr);
    return $filterUrl;
}

/**
 * 检测非法字符,存在返回falase 否则返回true
 * @param string $data
 * @param array $words
 * @return bool
 */
function filterKword($data='',$words=array()){
    #如果words为空就读取项目所配置的需要验证的关键词
    $words=empty($words)?config::readConfig('filterWords'):$words;
    #判断data是否为空,如果为空就是全局验证,否则就是验证某个数据是否是否则字符
    if($data != ''){
         if(in_array($data,$words)){
             return false;
         }else{
             return $data;
         }
    }
     $request=rebuild_array(array_values(array_merge($GLOBALS['_POST'],$GLOBALS['_GET'],$GLOBALS['_COOKIE']))); //得到需要验证的数组
     $keywords=array_merge($words,$request);
     #只需要判断统计数组中所有的值出现的次数,如果存在就会有重复值,否则用户就是合法数据
     $countkeywords=array_count_values($keywords);
       foreach($countkeywords as $key=>$value){
            if($value > 1){
                unset($words,$request,$countkeywords);
                return false;
            }
        }
    unset($words,$request,$countkeywords);
    return true;
}
    /**
     * 反转义
     * @param type $data
     * @return type
     */
 function clean($data){
    if (is_array($data)){
        foreach ($data as $key => $value){
            $data[clean($key)] =clean($value);
        }
    }else{
        $data = stripslashes($data);
    }
    return $data;
}
    /**
     *获取数据对字符串转义
     * @param type $data
     * @return type
     */
 function addslashes_deep($data){
    if(empty($data)){
        return $data;
    }else{
        if(is_array($data)){
            foreach($data as $value ){
                addslashes_deep($value);
            }
        }else{
            return addslashes($data);
        }
    }
}

/**
 * @param $error_no
 * @param $error_msg
 * @param $error_file
 * @param $error_line
 */
function error($error_no, $error_msg, $error_file, $error_line){
    $error = null;
    $error_level = array('E_WARNING' => '警告:非致命错误', 'E_NOTICE' => '注意:程序发现了不严谨的地方',
        'E_ALL' => '程序报告:所有的错误、警告和建议', 'E_ERROR' => '严重错误:致命的运行错误', 'E_PARSE' =>
        '严重错误:程序编译解析错误', 'E_USER_NOTICE' => '注意:程序善意的提醒', 'E_CORE_ERROR' =>
        '启动时初始化过程中的致命错误', 'E_CORE_WARNING' => '启动时初始化过程中的警告(非致命性错)', 'E_COMPILE_ERROR' =>
        '编译时致命性错', 'E_COMPILE_WARNING' => '编译时警告(非致命性错)', 'E_USER_ERROR' => '自定义的错误消息',
        'E_USER_WARNING' => '自定义的警告消息', 'E_STRICT' => '兼容性和互操作性的建议', '1' =>
        '严重错误:致命的运行错误', '2' => '注意:程序发现了非致命错误', '4' => '严重错误:程序编译解析错误', '8' =>
        '注意:程序发现了不严谨的地方', '256' => '自定义的错误消息', '512' => '自定义的警告消息', '1024' =>
        '注意:程序善意的提醒', '2048' => '兼容性和互操作性的建议', '8191' => '程序报告:所有的错误、警告和建议');
    if (array_key_exists($error_no, $error_level)){
        $error .= '<b><font color="red">错误级别:' . $error_level[$error_no] .
            '</font></b><br />';
    }
    $error .= '<b>错误说明:</b>' . $error_msg . '<br />';
    $error .= '<b>发生错误文件名:</b>' . basename($error_file) . '<br />';
    $error .= '<b>发生错误行:</b>' . $error_line . '<br />';
    echo $error;
}
/**
 * @param $string
 * @param $key
 * @return mixed
 *  加密
 */
function encrypt($string, $key)
{
    $str_len = strlen($string);
    $key_len = strlen($key);
    for ($i = 0; $i < $str_len; $i++) {
        for ($j = 0; $j < $key_len; $j++) {
            $string[$i] = $string[$i] ^ $key[$j];
        }
    }
    return $string;
}

/**
 * @param $string
 * @param $key
 * @return mixed
 *  解密
 */
function decrypt($string, $key)
{
    $str_len = strlen($string);
    $key_len = strlen($key);
    for ($i = 0; $i < $str_len; $i++) {
        for ($j = 0; $j < $key_len; $j++) {
            $string[$i] = $key[$j] ^ $string[$i];
        }
    }
    return $string;
}

/**
 * 非递归遍历所有栏目
 */
function get_all_type($col_name)
{
    global $dbsql, $config;
    if(!$dbsql->get_is_link()){$dbsql->open(false,$config);}//连接数据库
    $type_rows = $dbsql->read($col_name,'arctype');//查询所有栏目
    $rows=array();
    $t=array();
    foreach($type_rows as $v)
    {
        $rows[$v['id']] = $v;
    }
    unset($type_rows);
    foreach ($rows as $id => $item)
    {
        if ($item['reid'])//判断是由上级栏目
        {
            $rows[$item['reid']]['son'][] = &$rows[$item['id']];//给上级栏目附加子级栏目
            $t[] = $id;//记录子级栏目ID
        }
    }
    foreach($t as $u)
    {
        unset($rows[$u]);//删除子级栏目
    }
    unset($t);
    return $rows;
}


/**
 * 递归遍历栏目可以用于单个父栏目遍历及父栏目下的父级栏目遍历
 *$id 当前需要查询栏目ID，
 */
function get_alone_type($col_name,$id,$rid)
{
    global $dbsql, $config, $rows;
    $row = array();
    $row_copy = '';

    if(!$dbsql->get_is_link()){$dbsql->open(false,$config);}//连接数据库
    $sql = 'SELECT '.$col_name.' FROM  `arctype`  where reid=\''.$id.'\' ORDER BY id ASC';
    $dbsql->query($sql);//获得当前栏目的子栏目
    while($row = $dbsql->fetch_str())
    {
        $rows[$id]['son'][$row['id']] = $row;//当前栏目的子栏目
        if($rid)
        {
            $rows[$rid]['son'][$id]['son'][$row['id']] = &$rows[$id]['son'][$row['id']];//把当前栏目给上级子栏目
        }
        $row_copy[] = $row;
    }

    if($row_copy != '')
    {
        foreach($row_copy as $v)
        {
            get_alone_type($col_name,$v['id'],$v['reid'],$rows);
        }
    }
    return $rows[$id];//返回当前栏目的自己栏目
}

/**更换白符
 *把所有的空白字符替换为$var;
 */
function html_space($str = '',$var = '')
{
    $add_str = array('%01','%02','%03','%04','%05','%06','%07','%08','%09','%0b','%0c','%0e','%0f','%20','%19','%18','%17','%16','%15','%14','%13','%12','%11','%10',' ','%1a','%1b','%1c','%1d','%1f');
    return str_replace($add_str,$var,$str);
}
/**
 *更安全的空白符去除
 */
function remove_all_space($str, $url_encoded = TRUE)
{
    $non_displayables = array();

    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)

    if ($url_encoded)
    {
        $non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

    do
    {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    }
    while ($count);

    return $str;
}
/**
 * 转义html实体
*对于一些HTML特别字符进行转义以免跨站脚本的攻击
*/
function html_entity($str = '')
{
    $add_str = array('<','>','.','/','\'',':',';','(',')','=','"','*','[',']','{','}','%3c','%3e','%2e','%2f','%27','%3a','%3b','%28','%29','%3d','%09','%22','%2a','%5b','%5d','%7b','%7d','$','%24','?','%3f');
    $html_str = array('&#60','&#62','&#46','&#47','&#39','&#58','&#59','&#40','&#41','&#61','&#34','&#42','&#91','&#93','&#123','&#125','&#60','&#62','&#46','&#47','&#39','&#58','&#59','&#40','&#41','&#61','<br/>','&#34','&#42','&#91','&#93','&#123','&#125','&#36;','&#36;','&#63;','&#63;');
    return str_replace($add_str,$html_str,$str);
}
/**
 *文件名安全
 */
function save_filename($str, $relative_path = FALSE)
{
    $bad = array(
        "../",
        "<!--",
        "-->",
        "<",
        ">",
        "'",
        '"',
        '&',
        '$',
        '#',
        '{',
        '}',
        '[',
        ']',
        '=',
        ';',
        '?',
        "%20",
        "%22",
        "%3c",		// <
        "%253c",	// <
        "%3e",		// >
        "%0e",		// >
        "%28",		// (
        "%29",		// )
        "%2528",	// (
        "%26",		// &
        "%24",		// $
        "%3f",		// ?
        "%3b",		// ;
        "%3d"		// =
    );

    if ( ! $relative_path)
    {
        $bad[] = './';
        $bad[] = '/';
    }
    $str = html_space($str,'');
    return str_replace($bad,'',$str);
}

/**
 * 显示错误页面对话框
 * @param string $error
 * @param string $url
 * @param string $go
 * @return bool
 */
function show_msg($error ='',$url='',$go='-1')
{
    $gourl = '';
    $errors = '<div style="margin:40px auto;padding:0px;border:1px solid #ccc;text-align:center;width:280px"><div style="border-bottom:1px solid #ccc; background: blue; width:auto; height:40px; text-align:center; padding-top:5px; color:#ffffff"><font>网站温馨提示</font></div><p>';

    if($url != ''){
        $url = $errors.$error.'</p><a href="http://'.$url.'">如果你的浏览器没反应，请点击这里</a></div><script>setTimeout("window.location.replace(\'http://'.$url.'\')","3000");</script>';
        exit($url);

        return true;
    }

    if($_SERVER['HTTP_REFERER'] != ''){
        $gourl = $errors.$error.'</p><a href="'.$_SERVER['HTTP_REFERER'].'">如果你的浏览器没反应，请点击这里</a></div><script>setTimeout("window.history.go(-1)","3000");</script>';

    }else{
        $host = $_SERVER['HTTP_HOST'] || $_SERVER['SERVER_NAME'];
        $gourl = $errors.$error.'</p><a href="http://'.$host.'">如果你的浏览器没反应，请点击这里</a></div><script>setTimeout("window.history.go(-1)","3000");</script>';

    }
    exit($gourl);
    return true;
}

/**
 * 遍历文件夹
 */
function read_folder_directory($dir)
{
    $listDir = array();
    if($handler = opendir($dir)) {
        while (($sub = readdir($handler)) !== FALSE) {
            if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
                if(is_file($dir."/".$sub)) {
                    $listDir[] = $sub;
                }elseif(is_dir($dir."/".$sub)){
                    $listDir[$sub] = read_folder_directory($dir."/".$sub);
                }
            }
        }
        closedir($handler);
    }
    return $listDir;
}

/**
 * 删除文件夹
 */
function delete_floder($dir)
{
    $listDir = array();
    $filepath = '';
    if($handler = opendir($dir)) {
        while (($sub = readdir($handler)) !== FALSE) {
            $filepath = $dir."/".ltrim($sub,'/');
            if ($sub != "." && $sub != ".." && $sub != "Thumb.db" && $sub != "Thumbs.db") {
                if(is_file($filepath)) {
                    unlink($filepath);
                }elseif(is_dir($dir."/".$sub)){
                    $listDir[] = $filepath;//记录文件
                    delete_floder($filepath); //遍历文件夹
                }
            }
        }
        closedir($handler);
    }

    foreach($listDir as $k)
    {
        rmdir($k);
    }
}
/**
 * 二维数组转一维数组
 */
 function rebuild_array($arr,$type=true){
     static $tmp=array();
     foreach($arr as $value){
           if(is_array($value)){
             rebuild_array($value);
            }else{
               $tmp[]=$value;
           }
     }
      return $tmp;
 }
 function gjj($str){
    $farr = array(
        "/\\s+/",
        "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
    );
    $str = preg_replace($farr,"",$str);
    return addslashes($str);
}
function zh_input_bb($array){
    if (is_array($array))
    {
        foreach($array as $k => $v)
        {
            $array[$k] = zh_input_bb($v);
        }
    }
    else
    {
        $array = gjj($array);
    }
    return $array;
}
/**
 * 创建目录
 */
function mk_dir($dirs,$mode=0755){
        set_time_limit(0);
        if(is_array($dirs)){
            foreach($dirs as $dir){
                self::mk_dir($dir,$mode);
             }
        }else if(is_string($dirs)){
            if (is_dir($dirs) || @mkdir($dirs,$mode,true));
        }
		unset($dirs,$mode);
        return true;
      }

/**
 * php 获取ie的版本
 * @return string
 */
function getMSIE() {
    $userAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    if (preg_match("msie 6", $userAgent)) {
        return '6';
    }else if(preg_match("msie 7", $userAgent)){
        return '7';
    }else if(preg_match("msie 8", $userAgent)){
        return '8';
    }else if(preg_match("msie 9", $userAgent)){
        return '9';
    }else if(preg_match("msie 10", $userAgent)){
        return '10';
    }
}

/**
 * php实现冒泡排序
 * @param $array
 * @return mixed
 */
function bubble_sort ($array)
{
    do {
        $again = false;
        for($ii=0; $ii<(count($array)-1); $ii++) {
            if($array[$ii] > $array[$ii+1]) {
                $temp = $array[$ii];
                $array[$ii] = $array[$ii+1];
                $array[$ii+1] = $temp;
                $again = true;
            }
        }
    } while ($again==true);

    return $array;
}

/**
 * xml转array
 * @param $obj
 * @return array|string
 */
function simplexml_obj2array($obj) {
    if( count($obj) >= 1 )
    {
        $result = $keys = array();

        foreach( $obj as $key=>$value)
        {
            isset($keys[$key]) ? ($keys[$key] += 1) : ($keys[$key] = 1);

            if( $keys[$key] == 1 )
            {
                $result[$key] = simplexml_obj2array($value);
            }
            elseif( $keys[$key] == 2 )
            {
                $result[$key] = array($result[$key], simplexml_obj2array($value));
            }
            else if( $keys[$key] > 2 )
            {
                $result[$key][] = simplexml_obj2array($value);
            }
        }
        return $result;
    }
    else if( count($obj) == 0 )
    {
        return (string)$obj;
    }
}

/**
 * 依据ip地址得到位置
 * @param $ip
 * @return string
 */
function lazdf($ip){
    $curl= curl_init();
    curl_setopt($curl,CURLOPT_URL,"http://www.ip138.com/ips138.asp?ip=".$ip);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $ipdz=curl_exec($curl);
    curl_close($curl);
    preg_match("/<ul class=\"ul1\"><li>(.*?)<\/li>/i",$ipdz,$jgarray);
    preg_match("/本站主数据：(.*)/i", $jgarray[1],$ipp);
    return  "<div class=\"global_widht global_zj zj\" style=\"background: none repeat scroll 0% 0% rgb(226, 255, 191); font-size: 12px; color: rgb(85, 85, 85); height: 30px; line-height: 30px; border-bottom: 1px solid rgb(204, 204, 204); text-align: left;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;欢迎来自&nbsp;<b>".$ipp[1]."</b>&nbsp;的朋友！</div>";
}

/**
 *
 */
function  import(){

}