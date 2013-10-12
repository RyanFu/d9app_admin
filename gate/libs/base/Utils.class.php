<?php
namespace Gate\Libs\Base;
/**
 * util工具集
 */

class Utils {

        public static function getTimeGapMsg($time_gap) {
            if ($time_gap <= 60) {
                $time_msg = '1分钟内';
            }
            elseif ($time_gap > 60 && $time_gap <= 120) {
                $time_msg = '1分钟前';
            }
            elseif ($time_gap > 120 && $time_gap <= 180) {
                $time_msg = '2分钟前';
            }
            elseif ($time_gap > 180 && $time_gap <= 600) {
                $time_msg = '3分钟前';
            }
            elseif ($time_gap > 600 && $time_gap <= 1800) {
                $time_msg = '10分钟前';
            }
            elseif ($time_gap > 1800 && $time_gap <= 3600) {
                $time_msg = '30分钟前';
            }
            elseif ($time_gap > 3600 && $time_gap <= 3 * 3600) {
                $time_msg = '1小时前';
            }
            elseif ($time_gap > 3 * 3600 && $time_gap <= 6 * 3600) {
                $time_msg = '3小时前';
            }
            elseif ($time_gap > 6 * 3600 && $time_gap <= 12 * 3600) {
                $time_msg = '6小时前';
            }
            elseif ($time_gap > 12 * 3600 && $time_gap <= 24 * 3600) {
                $time_msg = '12小时前';
            }
            elseif ($time_gap > 24 * 3600 && $time_gap <= 48 * 3600) {
                $time_msg = '1天前';
            }
            elseif ($time_gap > 48 * 3600 && $time_gap <= 72 * 3600) {
                $time_msg = '2天前';
            }
            else {
                $time_msg = '3天前';
            }
            return $time_msg;
    }

}
