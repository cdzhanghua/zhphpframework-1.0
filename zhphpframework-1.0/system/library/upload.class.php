<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stu
 * Date: 14-3-4
 * Time: 下午4:56
 * To change this template use File | Settings | File Templates.
 */

class upload {
    /**
     * @param $filePath:需要读取的图片路径
     * @return null|resource:返回这个图片;
     */
    function getImg($filePath){
        try{
            $imgType = getFileType($filePath);
            $imgType = $imgType == "jpg" ? "jpeg" :$imgType;
            $iamge = null;
            switch($imgType){
                case "jpeg" :
                    $iamge = imagecreatefromjpeg($filePath);
                    break;
                case "gif" :
                    $iamge = imagecreatefromgif($filePath);
                    break;
                case "png" :
                    $iamge = imagecreatefrompng($filePath);
                    break;
            }
            return $iamge;
        }
        catch(Exception $e){
            return null;
        }
    }

    /**
     * @param $img:需要加水印的图片资源
     * @param $markStr:水印文字
     * @param $markX:水印位置的横向起点
     * @param $markY:水印位置的纵向起点
     * @param $fontColor:水印文字的颜色
     * @param $fontSize:水印文字的大小
     * @return 如果没有异常则返回添加水印后的图片如果发生异常则返回false;
     */
    function addSatermark($img,$markStr,$markX,$markY,$fontColor,$fontSize){
        try{
            $imgWidth = 0;
            $imgHeight = 0;
            if($img){
                $imgWidth = imagesx($img);
                $imgHeight = imagesy($img);
            }
            //创建与图片同等大小的画布
            $canvas = imagecreatetruecolor($imgWidth, $imgHeight);
            //获取画布的背景颜色
            $bgColor = imagecolorallocate($canvas,255,255,255);
            //为画布添加背景颜色
            imagefill($canvas, 0, 0, $bgColor);
            //将图片资源copy到画布上;
            imagecopy($canvas, $img, 0, 0, 0, 0, $imgWidth, $imgHeight);
            //将水印文字以指定的颜色,指定的位置,指定的大小放入图片中
            imagestring($canvas, $fontSize, $markX, $markY, $markStr, $fontColor);
            //返回这个图片
            return $canvas;
        }
        catch(Exception $e){
            return false;
        }
    }

    /**
     * @param $image:需要缩小的图片资源
     * @param int $thumWidth:可选参数,如果不指定缩略后的大小则将按照比例缩放,缩放后最大的边为200px;
     * @param int $thumHeight:可选参数,高度
     * @return bool|resource:如果操作没有出现异常,则返回缩放后的资源,如果出现异常则返回false;
     */
    function thum($image,$thumWidth=0,$thumHeight=0){
        try{
            $imageWidth = 0;
            $imageHeight = 0;
            if($image){
                $imageWidth = imagesx($image);
                $imageHeight = imagesy($image);
            }
            $imageWidth_Old = $imageWidth;
            $imageHeight_Old = $imageHeight;
            echo $imageWidth_Old."___".$imageHeight_Old;
            //将最大的边设置为150px;
            if($thumWidth == 0  || $thumHeight == 0){
                $reckon  = $imageWidth > $imageHeight ? (150/$imageWidth) : (150/$imageHeight);
                if( $reckon < 1 ){
                    $thumWidth = floor($imageWidth * $reckon);
                    $thumHeight = $imageHeight * $reckon;
                }
                else{
                    $thumWidth = $imageWidth;
                    $thumHeight = $imageHeight;
                }
            }
            //创建一张画布,大小为计算后的或客户端代码设置的大小
            $canvas = imagecreatetruecolor($thumWidth,$thumHeight);
            //将图片copy到画布上
            imagecopyresampled ($canvas,$image,0,0,0,0,$thumWidth,$thumHeight,$imageWidth_Old,$imageHeight_Old);
            return $canvas;
        }
        catch(Exception $e){
            return false;
        }
    }

    /**
     * @param $img:文件对象;
     * @param $imageType:文件类型;
     * @param $newimagePath:文件的保存路径,这个路径中包含新图片的名称;
     * @return bool :返回操作是否成功
     * 将传递进来的图片对象按照指定的格式保存到指定的路径中,这个路径中的最后一级为文件的名称;
     */
    function keepImage($img,$imageType,$newimagePath){
        try{
            if($imageType && $newimagePath && $img){
                $imageType = $imageType == "jpg" ? "jpeg" :$imageType;
                switch($imageType){
                    case "jpeg" :
                        imagejpeg($img,$newimagePath);
                        break;
                    case "gif" :
                        imagegif($img,$newimagePath);
                        break;
                    case "png" :
                        imagepng($img,$newimagePath);
                        break;
                }
            }
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }

    /**
     * 判断来自客户端的文件是否符合规则,如果符合规则则将文件保存到指定的目录
     * @param $filePath:保存文件的指定目录,最后一级目录需要加上"/"
     * @param $fileTypeArr:允许的文件类型数组
     * @param $fileSize:允许的文件大小,以byte为单位
     * @param $fileInputName:上传文件input的name值
     * @param $fileName:文件名,可选参数,如果没有给定将按照源文件名命名
     * @return array:返回的数组通过"result"键来查看上传的状态
     * 注意,保存路径是本地的物理路径!
     */
    function upload_img($filePath,$fileTypeArr,$fileSize,$fileInputName,$fileName=null){
        try{
            $result = array();
            if( is_dir($filePath) !== true ){
                mkdir($filePath,0775);//在linux系统中必须给定第二个参数
            }
            if( !empty($_FILES) ){
                $uploadFile = $_FILES[$fileInputName];
                $fileNameArr = explode( "." , $uploadFile["name"] );
                $fileType = strtolower(end($fileNameArr));
                if($fileName == null ){
                    $fileName = $uploadFile['name'];
                }
                $fileName = iconv("UTF-8","GB2312",$fileName);//文件名转码
                if($uploadFile["size"] > $fileSize){
                    $result["result"] = "文件大小超出指定范围!";
                }
                else if( $uploadFile['error'] != 0 ){
                    $result["result"] = "上传异常:".$uploadFile['error']."!";
                }
                else if( in_array($fileType , $fileTypeArr) === false){
                    $result["result"] = "未允许的文件类型!";
                }
                else if( move_uploaded_file( $uploadFile["tmp_name"] , $filePath . $fileName.".".$fileType) ){
                    $result["result"] = true;
                }
                else{
                    $result["result"] = "未知异常!";
                }
                $result['type']=$fileType;
                return $result;
            }
        }
        catch(Exception $e){
            $result["result"] = "未知异常!";
            return $result;
        }
    }
}