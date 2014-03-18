<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 14-3-16
 * Time: 下午7:24
 * To change this template use File | Settings | File Templates.
 */
Header( "Content-type: image/gif");

#创建一幅图片
$im = ImageCreate(156, 142);
# 设置颜色
$black = ImageColorAllocate($im, 50, 50, 50);
$white = ImageColorAllocate($im, 255, 255, 255);
$orange = ImageColorAllocate($im, 255, 200, 0);
$yellow = ImageColorAllocate($im, 255, 255, 0);
$tan = ImageColorAllocate($im, 255, 255, 190);
$grey = ImageColorAllocate($im, 205, 205, 205);
$dkgrey = ImageColorAllocate($im, 140, 140, 140);

###灰线 ###
#边框线
ImageRectangle($im, 1, 1, 155, 141, $dkgrey);
#水平线
ImageRectangle($im, 1, 22, 155, 39, $dkgrey);
ImageRectangle($im, 1, 56, 155, 73, $dkgrey);
ImageRectangle($im, 1, 90, 155, 107, $dkgrey);
ImageRectangle($im, 1, 107, 155, 124, $dkgrey);
#垂直线
ImageRectangle($im, 23, 22, 45, 141, $dkgrey);
ImageRectangle($im, 67, 22, 89, 141, $dkgrey);
ImageRectangle($im, 111, 22, 133, 141, $dkgrey);

### 白线 ###
#外框线
ImageRectangle($im, 0, 0, 154, 140, $white);
#水平线
ImageRectangle($im, 0, 21, 154, 38, $white);
ImageRectangle($im, 0, 55, 154, 72, $white);
ImageRectangle($im, 0, 89, 154, 106, $white);
ImageRectangle($im, 0, 106, 154, 123, $white);
#垂直线
ImageRectangle($im, 22, 21, 44, 140, $white);
ImageRectangle($im, 66, 21, 88, 140, $white);
ImageRectangle($im, 110, 21, 132, 140, $white);

### 在上面写数字

$today = date( "d");
$month = date( "m");
$year = date( "Y");
$datecode = date( "Ymd");
$gif = '.gif';
$first=mktime(0,0,0,$month,1,$year);

$mon_yr=date( "F Y", $first);

$wd=date( "w",$first);
#if ($wd==0) { $wd=7;}
$lastday=date( "d",mktime(0,0,0,$month+1,0,$year));
$cur=-$wd+0;
$ver_position = 50;
for ($k=0;$k<6;$k++) {
    $day_position = 5;
    $last_row_used = 0;
    for ($i=0;$i<7;$i++ ) {
        $cur++;
        $sing_add = 0;
        if (($cur<=0) || ($cur>$lastday) ) $day_position = ($day_position + 22);
        else
        {
            $day_color = $grey;
            if ($day_position<10) $day_color = $tan;
            if ($cur==$today) $day_color = $yellow;

            if (strlen($cur)<2) {$sing_add = 4;}
            $fin_position = ($day_position + $sing_add);

            ImageTTFText($im, 12, 0, $fin_position, $ver_position, $day_color, "./fonts/arialbd.ttf", "$cur");

            $day_position = ($day_position + 22);
            $last_row_used = 1;
        }
    }
    $day_position = 5;
    if ($last_row_used) $ver_position = ($ver_position + 17);
}
# 月份和年份 （Arial字体、加粗、居中）
$spc = 23;
$st_add = 0;
$st = "$mon_yr";
$st_len = strlen($st);
$st_margin = (14 - $st_len);
if ($st_margin > 0) {$st_add = ($st_margin * 4);}
$spc = ($spc + $st_add);
ImageTTFText($im, 14, 0, $spc, 15, $white, "./fonts/arialbd.ttf", "$st");

# 星期的名字
ImageString($im, 2, 3, 23, "Sun", $orange);
ImageString($im, 2, 25, 23, "Mon", $orange);
ImageString($im, 2, 47, 23, "Tue", $orange);
ImageString($im, 2, 69, 23, "Wed", $orange);
ImageString($im, 2, 91, 23, "Thu", $orange);
ImageString($im, 2, 113, 23, "Fri", $orange);
ImageString($im, 2, 135, 23, "Sat", $orange);

if ($ver_position<140)
{
    $im_out = ImageCreate(156, 125);
    $out_black = ImageColorAllocate($im_out, 50, 50, 50);
    ImageRectangle($im, 1, 124, 155, 124, $dkgrey);
    ImageCopyResized($im_out, $im, 0, 0, 0, 0, 156, 125, 156, 125);
    ImageColorTransparent($im_out, $out_black);
    ImageGIF($im_out, "./dategif/$datecode$gif");
    ImageGIF($im_out);
    ImageDestroy($im);
    ImageDestroy($im_out);
}
else
{
    ImageColorTransparent($im, $black);
    ImageGif($im, APP_PATH.'data/',$datecode,$gif);
    ImageGif($im);
    ImageDestroy($im);
}