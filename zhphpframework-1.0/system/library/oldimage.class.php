<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
class oldimage{
    private $imageType; //图片类型
    private $srcimg_path; //读取源图像地址
    private $dstimg_path; //缩略图保持地址
    private $srcimg; //读取图片的资源
    private $dstimg; //图片资源
    private $dst_x;
    private $dst_y;
    public  static  $fontPath;
    public  static  function  imageConfig($fontPath){
       self::$fontPath=$fontPath;
    }

    /**
     * @param $srcimg_path
     * @param $dstimg_path
     * @return bool
     * 不改变图片大小压缩
     */
    public  function trueCompress($srcimg_path){
        $this->srcimg_path = $srcimg_path;
       //执行读取图像路径
        $this->readImage();
        //压缩后输出

        switch ($this->imageType) {
            case 'jpeg':
                header("Content-type: image/jpeg");
                imagejpeg($this->srcimg,'',2);
                break;
            case 'jpg':
                header("Content-type: image/jpeg");
                imagejpeg($this->srcimg, '',2);
                break;
            case 'png':
                header("Content-type: image/png");
                imagepng($this->srcimg,'',2);
                break;
            case 'gif':
                header("Content-type: image/gif");
                imagegif($this->srcimg,'',2);
                break;
            case 'wbmp':
                header("Content-type: image/wbmp");
                imagewbmp($this->srcimg,'',2);
                break;
            case 'gd2':
                header("Content-type: image/gd2");
                imagegd2($this->srcimg,'',2);
                break;
            case 'xbm':
                header("Content-type: image/xbm");
                imagexbm($this->srcimg,'',2);
                break;
            default:
                return false;
                break;
        }
        imagedestroy($this->srcimg);
         unset($this->srcimg);
        unset($this->srcimg_path);
       unset($this->dstimg_path);
        unset($this->imageType);
    }


    /**
     * $srcimg_path 读取图片的路径
     * $dstimg_path;  压缩后的保存路径
     * $pct  压缩比率 0-1 之间的任意值\
     *  压缩图
     */
    public function compress($srcimg_path, $dstimg_path, $pct){
        $this->srcimg_path = $srcimg_path;
        $this->dstimg_path = $dstimg_path;
        //执行读取图像路径
        $this->readImage();
        //获取图像高度
        $srcimgX = imagesx($this->srcimg);
        //获取图像高度
        $srcimgY = imagesy($this->srcimg);
        //创建画布
        if (is_integer($pct) || is_float($pct)) {
            $pct = (is_float($pct)) ? $pct : floatval($pct);
        }

        $dst_width = $srcimgX * $pct;
        $dst_height = $srcimgY * $pct;
        $this->dstimg = imagecreatetruecolor($dst_width, $dst_height);
        //为画布设置颜色
        $bgcolr = imagecolorallocate($this->dstimg, 255, 255, 255);
        //将画布颜色填充到画布资源中
        imagefill($this->dstimg, 0, 0, $bgcolr);
        //实现压缩
        imagecopyresized($this->dstimg, $this->srcimg, 0, 0, 0, 0, $dst_width, $dst_height,
            $srcimgX, $srcimgY);
        //保存压缩图
        $this->save();
    }
    private function readImage(){
        $type_array = explode('.', $this->srcimg_path);
        $this->imageType = $type_array[count($type_array) - 1];
        switch ($this->imageType) {
            case 'jpeg':
                $this->srcimg = imagecreatefromjpeg($this->srcimg_path);
                break;
            case 'jpg':
                $this->srcimg = imagecreatefromjpeg($this->srcimg_path);
                break;
            case 'png':
                $this->srcimg = imagecreatefrompng($this->srcimg_path);
                break;
            case 'gif':
                $this->srcimg = imagecreatefromgif($this->srcimg_path);
                break;
            case 'wbmp':
                $this->srcimg = imagecreatefromwbmp($this->srcimg_path);
                break;
            case 'gd2':
                $this->srcimg = imagecreatefromgd2($this->srcimg_path);
                break;
            case 'xbm':
                $this->srcimg = imagecreatefromxbm($this->srcimg_path);
                break;
            default:
                return false;
                break;
        }
    }
    private function save(){
        switch ($this->imageType) {
            case 'jpeg':
                imagejpeg($this->dstimg, $this->dstimg_path);
                break;
            case 'jpg':
                imagejpeg($this->dstimg, $this->dstimg_path);
                break;
            case 'png':
                imagepng($this->dstimg, $this->dstimg_path);
                break;
            case 'gif':
                imagegif($this->dstimg, $this->dstimg_path);
                break;
            case 'wbmp':
                imagewbmp($this->dstimg, $this->dstimg_path);
                break;
            case 'gd2':
                imagegd2($this->dstimg, $this->dstimg_path);
                break;
            case 'xbm':
                imagexbm($this->dstimg, $this->dstimg_path);
                break;
            default:
                return false;
                break;
        }
        imagedestroy($this->srcimg);
        imagedestroy($this->dstimg);
        unset($this->srcimg);
        unset($this->srcimg_path);
        unset($this->dstimg);
        unset($this->dstimg_path);
        unset($this->imageType);
    }
    /**
     *
     * @param unknown_type $imagePath 图片路径 可以是字符串 也可以是数组（如果批量的话）
     * @param unknown_type $str  水印图片
     * @param unknown_type $savepath  保存路径
     * @param unknown_type $markPos  水印的位置  0-9
     */
    public function write_watermark($imagePath, $str, $savepath, $markPos){
        //创建压缩的时候源图像 和压缩图像都加水印
        $this->srcimg_path = $imagePath;
        $this->readImage();
        //创建小图片
        $width = 120;
        $height = 30;
        $this->createNewPic($width, $height,$str);
        //设置图片所在的位置
        $this->setxy($markPos, $width, $height);
        //小图合并到大图
        imagecopymerge($this->srcimg, $this->dstimg, $this->dst_x, $this->dst_y, 0, 0, $width,
            $height, 60);
        switch ($this->imageType) {
            case 'jpeg':
                imagejpeg($this->srcimg, $savepath);
                break;
            case 'jpg':
                imagejpeg($this->srcimg, $savepath);
                break;
            case 'png':
                imagepng($this->srcimg, $savepath);
                break;
            case 'gif':
                imagegif($this->srcimg, $savepath);
                break;
            case 'wbmp':
                imagewbmp($this->srcimg, $savepath);
                break;
            case 'gd2':
                imagegd2($this->srcimg, $savepath);
                break;
            case 'xbm':
                imagexbm($this->srcimg, $savepath);
                break;
            default:
                return false;
                break;
        }
    }
    private function createNewPic($width, $height,$text){
        //创建画布
        $this->dstimg = imagecreatetruecolor($width, $height);
        //为画布设置颜色
        $bgcolr = imagecolorallocate($this->dstimg, 255, 255, 255);
        //将画布颜色填充到画布资源中
        imagefill($this->dstimg, 0, 0, $bgcolr);
        $pink = imagecolorallocate($this->srcimg, 255, 128, 255);
        $fontfile =self::$fontPath.'/simfang.ttf';
        imagettftext($this->dstimg, 30, 0, 0, 5, $pink, $fontfile, $text);
    }
    private function setxy($markPos, $width, $height)
    {
        //获取图像高度
        $srcimgX = imagesx($this->srcimg);
        //获取图像高度
        $srcimgY = imagesy($this->srcimg);
        switch ($markPos) {
            //随机
            case 0:
                $this->dst_x = rand(0, ($srcimgX - $width));
                $this->dst_y = rand(0, ($srcimgY - $height));
                break;
            //1为顶端居左
            case 1:
                $this->dst_x = 0;
                $this->dst_y = 0;
                break;
            //2为顶端居中
            case 2:
                $this->dst_x = ($srcimgX - $width) / 2;
                $this->dst_y = 0;
                break;
            //3为顶端居右
            case 3:
                $this->dst_x = ($srcimgX - $width);
                $this->dst_y = 0;
                break;
            //4为中部居左
            case 4:
                $this->dst_x = 0;
                $this->dst_y = ($srcimgX - $height) / 2;
                break;
            //5为中部居中
            case 5:
                $this->dst_x = ($srcimgX - $width) / 2;
                $this->dst_y = ($srcimgX - $height) / 2;
                break;
            //6为中部居右
            case 6:
                $this->dst_x = ($srcimgX - $width);
                $this->dst_y = ($srcimgX - $height) / 2;
                break;
            //7为底端居左
            case 7:
                $this->dst_x = 0;
                $this->dst_y = $srcimgX - $height;
                break;
            //8为底端居中
            case 8:
                $this->dst_x = ($srcimgX - $width) / 2;
                $this->dst_y = $srcimgX - $height;
                break;
            //9为底端居右
            case 9:
                $this->dst_x = $srcimgX - $width;
                $this->dst_y = $srcimgX - $height;
                break;
            default: //随机
                $this->dst_x = rand(0, ($srcimgX - $width));
                $this->dst_y = rand(0, ($srcimgY - $height));
                break;
        }
    }
}
?>