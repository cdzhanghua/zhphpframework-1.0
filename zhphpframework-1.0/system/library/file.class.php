<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
final class file {
    /**
     * 创建目录
     * @param type $dir
     * @return boolean
     */
    public static function mk_dir($dirs,$mode=0755){
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
     * @param $files
     * @return bool
     */
    public static  function touchFile($files){
        if(is_array($files)){
            $count=count($files);
            for($i=0;$i<$count;$i++){
                if( ! is_file($files[$i])){
                    file_put_contents($files[$i],'');
                    touch($files[$i],0775);
                }
                chmod($files[$i],0775);
            }
            unset($files,$count);
        }
        return true;
    }

    /**指定读取文件从多少行 到多少行
     *
     * @param $file
     * @param $startLine
     * @param $endLine
     * @return bool|string
     */
    public  static  function  GetFilePart($file, $startLine, $endLine){
        if($endLine< 0) {
        return false;
        }
        $return = '';
        $files = file($file);
        if($startLine < 0) {
            $startLine = 0;
        }
        $iCpt = count($file);
        if($iCpt < $endLine) {
            $endLine = $iCpt;
        }
        for($i = $startLine; $i <= $endLine; $i++)
        {
            //if($i < count($files) && $i >= 0)
            $return .= $files[$i];
        }

        return $return;
}

 
   public  static  function  download($fileName){
      $file = $fileName; #获取文件
      $filename = basename($file); #得到文件名
      $ua = $_SERVER["HTTP_USER_AGENT"]; #得到浏览器
      header("Content-type: application/octet-stream");#发送一个下载头
      if (preg_match("/MSIE/", $ua)) { // 如果是ie 浏览器 设置下载的文件名
          $encoded_filename = rawurlencode($filename);
          header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
      } else if (preg_match("/Firefox/", $ua)) {
          header("Content-Disposition: attachment; filename=" . $filename);
      } else {
          header('Content-Disposition: attachment; filename="' . $filename . '"');
      }
      header("Content-Length: ". filesize($filename));  //告诉浏览器当前文件的大小
      readfile($file);
}

    /**
     * curl 远程调用
     * @param $urls
     * @param $delay
     * @return array
     */
    public  static  function rolling_curl($urls, $delay) {
       $queue = curl_multi_init();
       $map = array();
       foreach ($urls as $url) {
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_TIMEOUT, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_NOSIGNAL, true);
           curl_multi_add_handle($queue, $ch);
           $map[(string) $ch] = $url;
       }
       $responses = array();
       do {
           while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
           if ($code != CURLM_OK) {
               break;
           }
           while ($done = curl_multi_info_read($queue)) {
               $info = curl_getinfo($done['handle']);
			       $error = curl_error($done['handle']);
			       $results = callback(curl_multi_getcontent($done['handle']), $delay);
			       $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results');
                   curl_multi_remove_handle($queue, $done['handle']);
			       curl_close($done['handle']);
		    }
           if ($active > 0) {
			   curl_multi_select($queue, 0.5);
		   }

	} while ($active);

	curl_multi_close($queue);
	return $responses;
} 
}