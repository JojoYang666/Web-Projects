<?php
/**
 * Created by PhpStorm.
 * User: lyndon
 * Date: 17-1-3
 * Time: 下午8:44
 */

namespace App\Exceptions;


use League\Flysystem\Exception;

/**
 * 链接微信时出现的异常
 * Class WechatException
 * @package App\Exceptions
 */
class WechatException extends Exception
{

    const NO_TICKET = '没有第三方平台的ticket';
    const ERROR = '出错，请稍后重试';
    const NO_AUTH_CODE = '没有授权码';
    const POST_ERROR = 'POST数据失败';
    const NO_PLATFORM = '未找到微信平台，请确定已经绑定了微信公众号';
    const NO_OPENID = '未能获取用户微信信息！';
    const NO_UNIONID = '为统一账号，请前往微信开放平台（open.weixin.qq.com）绑定公众号';
    const NO_TOKEN = '未能获取微信授权码，请重新授权';
}