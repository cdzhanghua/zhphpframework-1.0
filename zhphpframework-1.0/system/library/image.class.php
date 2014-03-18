<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 张华
 * Date: 14-3-8
 * Time: 下午12:21
 * QQ: 746502560@qq.com
 * To change this template use File | Settings | File Templates.
 */

final class image {
    private $srcimage;
    private  $dstimage;
    /**
     * php缩略图函数  等比例无损压缩，可填充补充色
     * @param $srcimage 要缩小的图片
     * @param $dstimage 要保存的图片
     * @param $dst_width 缩小的宽度
     * @param $dst_height 缩小的高度
     * @param $backgroundcolor 补充颜色 默认为 白色
     */
    public   function imagezoom( $srcimage, $dstimage,  $dst_width, $dst_height, $backgroundcolor='#ffffff' ) {
         $this->fileNameGbk($srcimage, $dstimage);
        $dstimg = imagecreatetruecolor( $dst_width, $dst_height );
        $color = imagecolorallocate($dstimg
            , hexdec(substr($backgroundcolor, 1, 2))
            , hexdec(substr($backgroundcolor, 3, 2))
            , hexdec(substr($backgroundcolor, 5, 2))
        );
        imagefill($dstimg, 0, 0, $color);

        if ( !$arr=getimagesize($this->srcimage) ) {
            echo "要生成缩略图的文件不存在";
            exit;
        }

        $src_width = $arr[0];
        $src_height = $arr[1];
        $srcimg = null;
        $method = $this->getcreatemethod( $this->srcimage );
        if ( $method ) {
            eval( '$srcimg = ' . $method . ';' );
        }

        $dst_x = 0;
        $dst_y = 0;
        $dst_w = $dst_width;
        $dst_h = $dst_height;
        if ( ($dst_width / $dst_height - $src_width / $src_height) > 0 ) {
            $dst_w = $src_width * ( $dst_height / $src_height );
            $dst_x = ( $dst_width - $dst_w ) / 2;
        } elseif ( ($dst_width / $dst_height - $src_width / $src_height) < 0 ) {
            $dst_h = $src_height * ( $dst_width / $src_width );
            $dst_y = ( $dst_height - $dst_h ) / 2;
        }

        imagecopyresampled($dstimg, $srcimg, $dst_x
            , $dst_y, 0, 0, $dst_w, $dst_h, $src_width, $src_height);

        // 保存格式
        $arr = array( 'jpg' => 'imagejpeg' , 'jpeg' => 'imagejpeg', 'png' => 'imagepng' , 'gif' => 'imagegif');
        $suffix = strtolower( array_pop(explode('.', $this->dstimage ) ) );
        if (!in_array($suffix, array_keys($arr)) ) {
            echo "保存的文件名错误";
            exit;
        } else {
            eval( $arr[$suffix] . '($dstimg, "'.$this->dstimage.'");' );
        }

        imagejpeg($dstimg, $this->dstimage);
        imagedestroy($dstimg);
        imagedestroy($srcimg);

    }


   public  function getcreatemethod( $file ) {
        $arr = array(
            '474946' => "imagecreatefromgif('$file')"
        , 'FFD8FF' => "imagecreatefromjpeg('$file')"
        , '424D' => "imagecreatefrombmp('$file')"
        , '89504E' => "imagecreatefrompng('$file')"
        );
        $fd = fopen( $file, "rb" );
        $data = fread( $fd, 3 );

        $data = $this->str2hex($data);

        if ( array_key_exists( $data, $arr ) ) {
            return $arr[$data];
        } elseif ( array_key_exists( substr($data, 0, 4), $arr ) ) {
            return $arr[substr($data, 0, 4)];
        } else {
            return false;
        }
    }

   public  function str2hex( $str ) {
        $ret = "";

        for( $i = 0; $i < strlen( $str ) ; $i++ ) {
            $ret .= ord($str[$i]) >= 16 ? strval( dechex( ord($str[$i]) ) )
                : '0'. strval( dechex( ord($str[$i]) ) );
        }
    return strtoupper( $ret );
    }

    /**
     * 解决中文乱码问题
     * @param $srcimage
     * @param $dstimage
     */
    public  function fileNameGbk($srcimage, $dstimage){
        if ( PHP_OS == 'WINNT' ) {
            $srcimage = iconv('UTF-8', 'GBK', $srcimage);
            $dstimage = iconv('UTF-8', 'GBK', $dstimage);
        }
        $this->srcimage=$srcimage;
        $this->dstimage=$dstimage;
    }
}