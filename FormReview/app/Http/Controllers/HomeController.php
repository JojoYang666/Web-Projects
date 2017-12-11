<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Web\WechatController;
use App\User;
use App\UserInfo;
use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{

    const BIND_WECHAT_SECRAT = 'bind_wechat_';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'bindWechat']);
    }

    /**
     * 个人中心
     */
    public function index()
    {
        $info = Auth::user()->info;
        $key = md5(self::BIND_WECHAT_SECRAT . Auth::user()->phone);
        return view('home', [
            'userInfo' => $info,
            'bindUrl' => action('HomeController@bindWechat', ['id' => Auth::user()->id, 'key' => $key])
        ]);
    }

    /**
     * 更新用户信息
     * @param Request $request
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'value' => 'required',
            'model' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 1, 'error' => '值不可为空']);
        }
        $model = $request->input('model');
        $name = $request->input('name');
        $value = $request->input('value');
        if ($name == 'phone' || $name == 'email') {
            $code = $request->input('code');
            $token = Cache::get('code_' . $value);
            if(empty($code)||$code!=$token){
                return response()->json(['status' => 1, 'error' => '验证码不正确']);
            }
        }
        if ($model == User::class) {
            $user = User::findOrFail(Auth::user()->id);
            $user->$name = $value;
            $user->save();
        } elseif ($model == UserInfo::class) {
            $user = UserInfo::where('user_id', Auth::user()->id)->first();
            $user->$name = $value;
            $user->save();
        } else {
            return response()->json(['status' => 1, 'error' => '没有此model']);
        }
        return response()->json(['status' => 0]);
    }

    /**
     * save user info
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveInfo(Request $request)
    {
        $info = new UserInfo();
        $info->user_id = Auth::user()->id;
        $info->realname = $request->input('realname');
        $info->number = $request->input('number');
        $info->class = $request->input('class');
        $info->grade = $request->input('grade');
        $info->major = $request->input('major');
        $info->school = $request->input('school');
        $info->eductional_system = intval($request->input('eductional_system'));
        $info->college = $request->input('college');
        $info->zone = $request->input('zone');
        $info->address = $request->input('address');
        $info->save();
        return redirect()->route('home.index');

    }

    /**
     * 绑定微信信息
     */
    public function bindWechat(Request $request, $id, $key)
    {
        if (isWechat()) {
            $user = User::find($id);
            if ($user && $key == md5(self::BIND_WECHAT_SECRAT . $user->phone)) {
                $wechat = new WechatController(env('DEFAULT_APPID'));
                $unionid = $wechat->getOpenid($request, WechatController::UNIONID);
                if ($unionid && Cache::has($unionid)) {
                    $userinfo = json_decode(Cache::get($unionid));
                    $user->openid = $userinfo->openid;
                    $user->nickname = $userinfo->nickname;
                    $user->sex = $userinfo->sex;
                    $user->province = $userinfo->province;
                    $user->city = $userinfo->city;
                    $user->country = $userinfo->country;
                    $user->headimgurl = $userinfo->headimgurl;
                    $user->privilege = json_encode($userinfo->privilege);
                    $user->unionid = $userinfo->unionid;
                    $user->save();
                    return view('notice', ['notice' => '绑定成功']);
                } else {
                    return view('notice', ['notice' => '绑定失败，请稍后重试']);
                }
            }
            return view('notice', ['notice' => '未找到此用户或key值不对']);
        } else {
            return view('notice', ['notice' => '请通过微信访问', 'qrcode' => true]);
        }
    }

}
