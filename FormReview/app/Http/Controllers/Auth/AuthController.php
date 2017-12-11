<?php

namespace App\Http\Controllers\Auth;

use anlutro\cURL\Laravel\cURL;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;


class AuthController extends Controller
{
    const APP_NAME = '表单管理大师';
    const EMAIL_TO = '尊敬的用户';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/web/form';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'sendCode']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param Request $request
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request, array $data)
    {
        $phone = $request->input('phone');
        $email = $request->input('email');
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|numeric|unique:users',
            'code' => 'code:' . $phone,
            'ecode' => 'code:' . $email,
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function getCredentials(Request $request)
    {
        $login = $request->get('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        return [
            $field => $login,
            'password' => $request->get('password'),
        ];
    }

    /**
     * 发送验证码到手机/邮箱
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCode(Request $request, $type = 'phone')
    {
        if ($type == 'phone') {
            $phone = $request->input('phone');
            if (empty($phone) || !preg_match('/^1[34578]\d{9}$/', $phone)) {
                return response()->json(['status' => 1, 'error' => '手机格式不正确']);
            }
            $token = rand(100000, 999999);
            //发送验证码
            $url = "http://sms.bechtech.cn/Api/send/data/json?accesskey=4788&secretkey=0518bd35d50c18e8151d8b7f7c886dcff8ebeeae&mobile={$phone}&content=" . urlencode("动态验证码：{$token}（10分钟内有效）。【表单管理大师】");
            $json = cURL::get($url);
            $arr = json_decode($json->body, true); //格式化返回数组
            if ($arr['result'] == '01') {
                Cache::put('code_' . $phone, $token, 10);
                return response()->json(['status' => 0]);
            } else {
                return response()->json(['status' => 1, 'error' => '发送失败，请稍后重试']);
            }
        } else {
            $email = $request->input('email');
            if (empty($email) || !preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/',
                    $email)
            ) {
                return response()->json(['status' => 1, 'error' => '邮箱格式不正确']);
            }
            $token = rand(100000, 999999);
            //发送验证码
            Mail::send('auth.emails.code', ['token' => $token], function ($m) use ($email) {
                $m->from(env('MAIL_USERNAME'), env('APP_NAME',self::APP_NAME));

                $m->to($email, self::EMAIL_TO)->subject('邮箱修改验证码');
            });
            Cache::put('code_' . $email, $token, 10);
            return response()->json(['status' => 0]);
        }
    }
}
