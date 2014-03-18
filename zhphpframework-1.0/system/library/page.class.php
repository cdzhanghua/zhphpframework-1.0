<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
class page {
    public static function pageBreak($content){
        $content=$content;
        $pattern='/<hr style=\"page-break-after:always;\" class=\"ke-pagebreak\" \/>/';
        $strSplit=preg_split($pattern,$content,-1,PREG_SPLIT_NO_EMPTY);
        $count=count($strSplit);
        $outStr="";
        $i=1;
        if($count>1){
            $outStr="<div id='page_break'>";
            foreach($strSplit as $value){
                if($i<=1){
                    $outStr.="<div id='page_$i'>$value</div>";
                }else{
                    $outStr.="<div id='page_$i'class='collapse'>$value</div>";
                }
                $i++;
            }
            $outStr.="</div><div class='num'>";
            for($i=1;$i<=$count;$i++){
                $outStr.='<span>第'.$i.'页</span>';
            }
            $outStr.="</div>";
            return $outStr;
        }else{
            return $content;
          }
      }
     public static function  dopages($curent_page, $totalcontent, $perpage){
    $totalpages = $totalcontent / $perpage;

    if($curent_page !== 1){
        $preid = $curent_page - 1;
        $output .= '<a href="'.$preid.'">Previous</a> ';
    }

    while ( $counter <= $totalpages ) {
        if($counter == $curent_page){
            $output .= '<a href="'.$counter.'"><b>'.$counter.'</b></a> ';
        }else {
            $output .= '<a href="'.$counter.'">'.$counter.'</a> ';
        }
        $counter = $counter + 1;
    }

    if($curent_page !== $totalpages){
        $neid = $curent_page + 1;
        $output .= '<a href="'.$neid.'">Next</a> ';
    }


    return $output;
}
}