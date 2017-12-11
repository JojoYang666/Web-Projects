<?php
/**
 * 全局自定义函数
 * Created by PhpStorm.
 * User: lyndon
 * Date: 17-1-19
 * Time: 上午10:14
 */
if (!function_exists('isWechat')) {
    function isWechat()
    {
        $result = isset($_SERVER['HTTP_USER_AGENT']) ?
            strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') : false;
        return $result !== false;
    }
}

if (!function_exists('cdnAsset')) {
    function cdnAsset($url)
    {
        return asset($url);
        //不能切换到cdn，字体文件加载不出来
        return 'http://assets.meizucampus.com/form' . $url;
    }
}

if (!function_exists('status2zh')) {
    function status2zh($now_status,$reviewTimes=10) {
        $status = ['初审', '复审', '三审', '四审', '五审', '六审', '七审', '八审', '九审', '十审'];
        $now_status = intval($now_status);
        $reviewTimes = intval($reviewTimes);
        if ($now_status >= $reviewTimes) {
            return '终审通过';
        }
        else if ($now_status < 0) {
            return $status[-$now_status - 1] . '拒绝';
        }
        else {
            return $status[$now_status] . '中';
        }
    }
}
