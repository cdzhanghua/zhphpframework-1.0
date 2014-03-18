<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */
$auto = 1;
function instance($dir){
    $basedir=isset($dir)?$dir:'.';
    checkdir($basedir);
}
function checkdir($basedir){
    if ($dh = opendir($basedir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..'){
                if (!is_dir($basedir."/".$file)) {
                    echo "文件名：: $basedir/$file ".checkBOM("$basedir/$file")." <br>";
                }else{
                    $dirname = $basedir."/".$file;
                    checkdir($dirname);
                }
            }
        }
        closedir($dh);
    }
}
function checkBOM ($filename) {
    global $auto;
    $contents = file_get_contents($filename);
    $contents = preg_replace( '/^(\xef\xbb\xbf)/', '', $contents );
    $charset[1] = substr($contents, 0, 1);
    $charset[2] = substr($contents, 1, 1);
    $charset[3] = substr($contents, 2, 1);
    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        if ($auto == 1) {
            $rest = substr($contents, 3);
            rewrite ($filename, $rest);
            return ("<span style='color: #ff0000;'>找到bom错误,框架已自动删除.</span>");
        } else {
            return ("<span style='color: #ff0000;'>找到了一个bom 错误，请你更改.</span>");
        }
    }
    else return ("没有找到bom错误.");
}
function rewrite ($filename, $data) {
    $filenum = fopen($filename, "w");
    flock($filenum, LOCK_EX);
    fwrite($filenum, $data);
    fclose($filenum);
}
?>
