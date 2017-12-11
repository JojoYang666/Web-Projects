/**
 * Created by lyndon on 17-3-1.
 */
/**
 * 审核次数转化为中文
 * @param i
 * @returns {string}
 */
function reviewTime2zh(i) {
    var $status = ['初审', '复审', '三审', '四审', '五审', '六审', '七审', '八审', '九审', '十审'];
    return $status[i];
}
function status2zh($now_status,$reviewTimes) {
    var $status = ['初审', '复审', '三审', '四审', '五审', '六审', '七审', '八审', '九审', '十审'];
    $now_status = parseInt($now_status);
    $reviewTimes = parseInt($reviewTimes);
    if ($now_status >= $reviewTimes) {
        return '终审通过';
    }
    else if ($now_status < 0) {
        return $status[-$now_status - 1] + '拒绝';
    }
    else {
        return $status[$now_status] + '中';
    }
}

// 验证手机号
function isPhone(phone) {
    var pattern = /^1[34578]\d{9}$/;
    return pattern.test(phone);
}

//获得URL参数
function getQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}

//移除导致乱码的class
function removeIconClass() {
    var $icon = $('i');
    $icon.removeClass('icon-chevron-down');
    $icon.removeClass('icon-refresh');
    $icon.removeClass('icon-list-alt');
    $icon.removeClass('icon-th');
    $icon.removeClass('icon-share');
    $icon.removeClass('icon-clear');
}
