<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Web\WechatController;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class WechatRedirect
{
    /**
     * 如果通过微信浏览器打开，那么首先登录
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isWechat() && Auth::guest()) {
            $wechat = new WechatController(env('DEFAULT_APPID'));
            $unionid = $wechat->getOpenid($request, WechatController::UNIONID);
            if ($unionid) {
                $user = User::where('unionid', $unionid)->first();
                if ($user) {
                    Auth::login($user);
                }
            }
        }
        return $next($request);
    }
}
