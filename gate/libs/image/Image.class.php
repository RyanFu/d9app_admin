<?php
namespace Gate\Libs\Image;

class Image {

    /**
     * 判断图片是否是gif动画,代码来自
     * http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
     *
     */
    public static function isAnimatedGif($filecontents) {
        $str_loc = 0;
        $count = 0;
        while ($count < 2) { # There is no point in continuing after we find a 2nd frame
           $where1 = strpos($filecontents,"\x00\x21\xF9\x04",$str_loc);
           if ($where1 === FALSE) {
               break;
           }
           else
           {
               $str_loc=$where1+1;
               $where2=strpos($filecontents,"\x00\x2C",$str_loc);
               if ($where2 === FALSE)
               {
                   break;
               }
               else
               {
                   if ($where1+8 == $where2)
                   {
                           $count++;
                   }
                   $str_loc=$where2+1;
               }
           }
        }
        if ($count > 1) {
            return TRUE;

        }
        else {
            return FALSE;
        }
    }
}
