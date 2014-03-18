<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-3-16
 * Time: 下午7:17
 * To change this template use File | Settings | File Templates.
 */

class Email{
//---全局变量
    var $mailTo = ""; // 邮件目的地址数组
    var $mailCC = ""; // 抄送人地址
    var $mailBCC = ""; // 暗送人地址
    var $mailFrom = ""; // 发送人地址
    var $mailSubject = ""; // 邮件主题
    var $mailText = ""; // 纯文本信息
    var $mailHTML = ""; // html 文本信息
    var $mailAttachments = ""; // 附件数组

    /*******************************************************************************
    函数： setTo($inAddress)
    描述： 设置电子邮件地址
    参数： $inAddress 是 string 类型
    按逗号把各个邮件地址分离出来
    返回值： 如果成功则返回true
     *******************************************************************************/
    function setTo($inAddress){
//--把逗号做分隔符分离邮件地址
        $addressArray = explode( ",",$inAddress);
//--检查每一个邮件地址，如果没有错误就退出。
        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }
//--如果所有的邮件地址都正确，那么调用implode把邮件地址恢复
        $this->mailTo = implode($addressArray, ",");
        return true;
    }
    /*******************************************************************************
    函数： setCC($inAddress)
    描述：设置邮件的抄送地址
    参数： $inAddress 是String型
    按逗号把各个邮件地址分离出来
    返回值： 如果成功则返回true
     *******************************************************************************/
    function setCC($inAddress){

        $addressArray = explode( ",",$inAddress);

        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }

        $this->mailCC = implode($addressArray, ",");
        return true;
    }
    /*******************************************************************************
    函数： setBCC($inAddress)
    描述： 设置暗送邮件地址
    参数： $inAddress 是String型
    按逗号把各个邮件地址分离出来
    返回值： 如果成功则返回true
     *******************************************************************************/
    function setBCC($inAddress){

        $addressArray = explode( ",",$inAddress);

        for($i=0;$i<count($addressArray);$i++){
            if($this->checkEmail($addressArray[$i])==false) return false;
        }

        $this->mailBCC = implode($addressArray, ",");
        return true;
    }
    /*******************************************************************************
    函数： setFrom($inAddress)
    描述： 设置邮件发送人地址
    参数： $inAddress 是 string 型 (只有一个邮件地址)
    返回值：如果成功则返回true
     *******************************************************************************/
    function setFrom($inAddress){
        if($this->checkEmail($inAddress)){
            $this->mailFrom = $inAddress;
            return true;
        }
        return false;
    }
    /*******************************************************************************
    函数： setSubject($inSubject)
    描述： 设置邮件主题
    参数：$inSubject 是 string 类型
    返回值：如果成功则返回true
     *******************************************************************************/
    function setSubject($inSubject){
        if(strlen(trim($inSubject)) > 0){
            $this->mailSubject = ereg_replace( "\n", "",$inSubject);
            return true;
        }
        return false;
    }
    /*******************************************************************************
    函数： setText($inText)
    描述：设置邮件纯文本内容
    参数： $inText 是 string 类型
    返回值：如果成功则返回true
     *******************************************************************************/
    function setText($inText){
        if(strlen(trim($inText)) > 0){
            $this->mailText = $inText;
            return true;
        }
        return false;
    }
    /*******************************************************************************
    函数： setHTML($inHTML)
    描述：设置邮件HTML文本内容
    参数： $inHTML 是 string 类型
    返回值：如果成功则返回true
     *******************************************************************************/
    function setHTML($inHTML){
        if(strlen(trim($inHTML)) > 0){
            $this->mailHTML = $inHTML;
            return true;
        }
        return false;
    }
    /*******************************************************************************
    函数： setAttachments($inAttachments)
    描述： 存贮附件字符串
    参数： $inAttachments 是一个包含了目录信息的String类型
    以逗号为分隔符
    返回值：如果成功则返回true
     *******************************************************************************/
    function setAttachments($inAttachments){
        if(strlen(trim($inAttachments)) > 0){
            $this->mailAttachments = $inAttachments;
            return true;
        }
        return false;
    }
    /*******************************************************************************
    函数： checkEmail($inAddress)
    描述：检查邮件地址是法合法
    参数：$inAddress 是 string 类型
    返回值：如果合法则返回true
     *******************************************************************************/
    function checkEmail($inAddress){
        return (ereg( "^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$inAddress));
    }
    /*******************************************************************************
    函数： loadTemplate($inFileLocation,$inHash,$inFormat)
    描述： 读取一个模板文件并替换一些宏定义的值
    参数：$inFileLocation 是 string 类型，记录目录信息
    $inHash 是 Hash 类型，是要被替代的值
    $inFormat 是 string 类型，可以是 "text" 或 "html"
    返回值：如果被load则返回true
     *******************************************************************************/
    function loadTemplate($inFileLocation,$inHash,$inFormat){
        /*
        模板文件应该有像下面那样的样子：
        Dear ~!UserName~,
        Your address is ~!UserAddress~
        */
//--指定模板的一些符号
        $templateDelim = "~";
        $templateNameStart = "!";
//--设置外部字串
        $templateLineOut = "";
//--打开模板文件
        if($templateFile = fopen($inFileLocation, "r")){
//--循环分析文件，一行一行的分析
            while(!feof($templateFile)){

                $templateLine = fgets($templateFile,1000);
//--分开文件的每一行，并把其存于数组中，并且规范其语句
                $templateLineArray = explode($templateDelim,$templateLine);

                for( $i=0; $i<count($templateLineArray);$i++){
//--从0的位置开始寻找 $templateNameStart
                    if(strcspn($templateLineArray[$i],$templateNameStart)==0){
//--在 $templateNameStart 之后得到宏定义的名字
                        $hashName = substr($templateLineArray[$i],1);
//--替代宏定义的名字
                        $templateLineArray[$i] = ereg_replace($hashName,(string)$inHash[$hashName],$hashName);
                    }
                }
//--输出字串数全，并把它加入到外部数组中
                $templateLineOut .= implode($templateLineArray, "");
            }
//--关闭文件
            fclose($templateFile);
//--按固定的格式设置邮件内容
            if( strtoupper($inFormat)== "TEXT" ) return($this->setText($templateLineOut));
            else if( strtoupper($inFormat)== "HTML" ) return($this->setHTML($templateLineOut));
        }
        return false;
    }
    /*******************************************************************************
    函数：getRandomBoundary($offset)
    描述：返回一个边界随机值
    参数： $offset 是 integer 类型
    返回：字符串
     *******************************************************************************/
    function getRandomBoundary($offset = 0){
        srand(time()+$offset);
        return ( "----".(md5(rand())));
    }
    /*******************************************************************************
    函数：getContentType($inFileName)
    描述：为文件种类返回一个 内容的种类
    参数：$inFileName 是一个 string 类型，记录文件名(可以含路径)
    返回：字串
     *******************************************************************************/
    function getContentType($inFileName){
//--剥去路径
        $inFileName = basename($inFileName);
//--检查文件扩展名
        if(strrchr($inFileName, ".") == false){
            return "application/octet-stream";
        }
//--得到文件扩展名，并判断文件类型
        $extension = strrchr($inFileName, ".");
        switch($extension){
            case ".gif": return "image/gif";
            case ".gz": return "application/x-gzip";
            case ".htm": return "text/html";
            case ".html": return "text/html";
            case ".jpg": return "image/jpeg";
            case ".tarLib": return "application/x-tarLib";
            case ".txt": return "text/plain";
            case ".zip": return "application/zip";
            default: return "application/octet-stream";
        }
        return "application/octet-stream";
    }
    /*******************************************************************************
    函数： formatTextHeader
    描述： 为文本返回一个格式化过的头信息
    参数： 没有
    返回： 字串
     *******************************************************************************/
    function formatTextHeader(){
        $outTextHeader = "";
        $outTextHeader .= "Content-Type: text/plain; charset=us-ascii\n";
        $outTextHeader .= "Content-Transfer-Encoding: 7bit\n\n";
        $outTextHeader .= $this->mailText. "\n";
        return $outTextHeader;
    }
    /*******************************************************************************
    函数： formatHTMLHeader
    描述： 返回一个HTML的头信息
    参数： 没有
    返回： 字串
     *******************************************************************************/
    function formatHTMLHeader(){
        $outHTMLHeader = "";
        $outHTMLHeader .= "Content-Type: text/html; charset=us-ascii\n";
        $outHTMLHeader .= "Content-Transfer-Encoding: 7bit\n\n";
        $outHTMLHeader .= $this->mailHTML. "\n";
        return $outHTMLHeader;
    }
    /*******************************************************************************
    函数： formatAttachmentHeader($inFileLocation)
    描述： 返回一个附件的头信息
    参数： $inFileLocation 是相关目录的String型变量
    返回： 字串
     *******************************************************************************/
    function formatAttachmentHeader($inFileLocation){
        $outAttachmentHeader = "";
//--通过文件夹的扩展名得到 content-type
        $contentType = $this->getContentType($inFileLocation);
//--如果是TEXT的类型，那么就用标准的7bit编码
        if(ereg( "text",$contentType)){
//--格式化信息头
            $outAttachmentHeader .= "Content-Type: ".$contentType. ";\n";
            $outAttachmentHeader .= ' name="'.basename($inFileLocation). '"'. "\n";
            $outAttachmentHeader .= "Content-Transfer-Encoding: 7bit\n";
            $outAttachmentHeader .= "Content-Disposition: attachment;\n"; //--other: inline
            $outAttachmentHeader .= ' filename="'.basename($inFileLocation). '"'. "\n\n";
            $textFile = fopen($inFileLocation, "r");
//--一行一行地检查文件
            while(!feof($textFile)){
                $outAttachmentHeader .= fgets($textFile,1000);
            }
//--关闭文件
            fclose($textFile);
            $outAttachmentHeader .= "\n";
        }
//--非TEXT类型用 64-bit 编码
        else{
//--格式头信息
            $outAttachmentHeader .= "Content-Type: ".$contentType. ";\n";
            $outAttachmentHeader .= ' name="'.basename($inFileLocation). '"'. "\n";
            $outAttachmentHeader .= "Content-Transfer-Encoding: base64\n";
            $outAttachmentHeader .= "Content-Disposition: attachment;\n"; //--other: inline
            $outAttachmentHeader .= ' filename="'.basename($inFileLocation). '"'. "\n\n";
//--调用 uuencode 命令
            exec( "uuencode -m $inFileLocation nothing_out",$returnArray);
//--加入每一行的返回值
            for ($i = 1; $i<(count($returnArray)); $i++){
                $outAttachmentHeader .= $returnArray[$i]. "\n";
            }
        }
        return $outAttachmentHeader;
    }
    /*******************************************************************************
    函数： send()
    描述： 发送邮件
    参数： 没有
    返回： 发送成功返回真
     *******************************************************************************/
    function send(){
//--把邮件头设为空
        $mailHeader = "";
//--加入抄送地址
        if($this->mailCC != "") $mailHeader .= "CC: ".$this->mailCC. "\n";
//--加入暗送地址
        if($this->mailBCC != "") $mailHeader .= "BCC: ".$this->mailBCC. "\n";
//--加入发送人地址
        if($this->mailFrom != "") $mailHeader .= "FROM: ".$this->mailFrom. "\n";

//---------------------------信息类型-------------------------------
//--TEXT文本
        if($this->mailText != "" && $this->mailHTML == "" && $this->mailAttachments == ""){
            return mail($this->mailTo,$this->mailSubject,$this->mailText,$mailHeader);
        }
//--HTML 和 TEXT
        else if($this->mailText != "" && $this->mailHTML != "" && $this->mailAttachments == ""){
//--得到一个随机边界
            $bodyBoundary = $this->getRandomBoundary();
//--格式化头信息
            $textHeader = $this->formatTextHeader();
            $htmlHeader = $this->formatHTMLHeader();
//--设置 MIME 版本
            $mailHeader .= "MIME-Version: 1.0\n";

            $mailHeader .= "Content-Type: multipart/alternative;\n";
            $mailHeader .= ' boundary="'.$bodyBoundary. '"';
            $mailHeader .= "\n\n\n";
//--加入信体和边界
            $mailHeader .= "--".$bodyBoundary. "\n";
            $mailHeader .= $textHeader;
            $mailHeader .= "--".$bodyBoundary. "\n";
//--加入HTML和结束边界
            $mailHeader .= $htmlHeader;
            $mailHeader .= "\n--".$bodyBoundary. "--";
//--发送信息
            return mail($this->mailTo,$this->mailSubject, "",$mailHeader);
        }
//--HTML 和 TEXT 和 附件
        else if($this->mailText != "" && $this->mailHTML != "" && $this->mailAttachments != ""){

            $attachmentBoundary = $this->getRandomBoundary();
//--为所有的部分和边界设置信息头
            $mailHeader .= "Content-Type: multipart/mixed;\n";
            $mailHeader .= ' boundary="'.$attachmentBoundary. '"'. "\n\n";
            $mailHeader .= "This is a multi-part message in MIME format.\n";
            $mailHeader .= "--".$attachmentBoundary. "\n";

//--TEXT 和 HTML--
            $bodyBoundary = $this->getRandomBoundary(1);
//--格式化头信息
            $textHeader = $this->formatTextHeader();
            $htmlHeader = $this->formatHTMLHeader();
//--设置 MIME 版本
            $mailHeader .= "MIME-Version: 1.0\n";
//--为所有的部分和边界设置信息头
            $mailHeader .= "Content-Type: multipart/alternative;\n";
            $mailHeader .= ' boundary="'.$bodyBoundary. '"';
            $mailHeader .= "\n\n\n";
//--加入信体和边界
            $mailHeader .= "--".$bodyBoundary. "\n";
            $mailHeader .= $textHeader;
            $mailHeader .= "--".$bodyBoundary. "\n";
//--加入 html 和 结尾边界
            $mailHeader .= $htmlHeader;
            $mailHeader .= "\n--".$bodyBoundary. "--";

//--得到附件文件名数组
            $attachmentArray = explode( ",",$this->mailAttachments);
//--检索第一个附件
            for($i=0;$i<count($attachmentArray);$i++){
//--附件分隔标志
                $mailHeader .= "\n--".$attachmentBoundary. "\n";
//--得到附件信息
                $mailHeader .= $this->formatAttachmentHeader($attachmentArray[$i]);
            }
            $mailHeader .= "--".$attachmentBoundary. "--";
            return mail($this->mailTo,$this->mailSubject, "",$mailHeader);
        }
        return false;
    }
}