<?php

namespace App\Http\Controllers\Web;

use anlutro\cURL\Laravel\cURL;
use App\Exceptions\WechatException;
use App\Http\Controllers\Web\Wxmsgcrypt\Wxbizmsgcrypt;
use App\RedisKey;
use App\Wechat\WechatPlatform;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use League\Flysystem\Exception;

class WechatController extends Controller
{

    const OPENID = 'openid';
    const UNIONID = 'unionid';
    public $component_appid;
    private $component_appsecret;
    private $component_token;
    private $component_key;
    public $appid;

    /**
     * Wechat constructor.
     */
    public function __construct($appid = '')
    {
        $this->middleware('auth', ['except' => ['auth', 'callback']]);
        $this->component_appid = env('COMPONENT_APPID');
        $this->component_appsecret = env('COMPONENT_APPSECRET');
        $this->component_token = env('COMPONENT_TOKEN');
        $this->component_key = env('COMPONENT_KEY');
        $this->appid = $appid;
    }

    /**
     * 展示微信平台列表，包括自己创建的和有权限管理的
     */
    public function showPlatforms()
    {
        return view('web/wechat/platforms');
    }

    public function getPlatforms()
    {
        $platforms = WechatPlatform::where('owner_id', Auth::user()->id)->get();
        return response()->json($platforms);
    }

    /**
     * 展示微信平台列表，包括自己创建的和有权限管理的
     */
    public function showTemplates()
    {
        return view('web/wechat/templates')->withWechatTemplates(Auth::user()->wechatPlatforms);
    }

    /**
     * 对应新增按钮
     * 放置“微信公众号授权”的入口，引导公众号运营者进入授权页
     */
    public function addPlatform()
    {
        $component_appid = $this->component_appid;
        try {
            $pre_auth_code = Cache::get('pre_auth_code') or $this->get_pre_auth_code();
        } catch (Exception $e) {
            return response()->json(['status' => 1, 'error' => $e->getMessage()]);
        }
        if (empty($pre_auth_code)) {
            return response()->json(['status' => 1, 'error' => WechatException::ERROR]);
        }
        $redirect_uri = urlencode(url('web/wechat/save-platform'));
        $url = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=$component_appid&pre_auth_code=$pre_auth_code&redirect_uri=$redirect_uri";
        return response()->json(['status' => 0, 'url' => $url]);
    }

    /**
     * 修改平台信息
     */
    public function editPlatform(Request $request)
    {
        $id = $request->input('id');
        $field = $request->input('field');
        $newValue = $request->input('newValue');
        $newValue = json_decode($newValue);
        $oldValue = $request->input('oldValue');
        $platform = WechatPlatform::find($id);
        if ($platform) {
            $platform->$field = $newValue->$field;
            $platform->save();
            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => 1]);
    }

    /**
     *保存授权码
     */
    public function savePlatform(Request $request)
    {
        if (empty($request->input('auth_code'))) {
            Log::info('没有auth_code');
            throw new WechatException(WechatException::NO_AUTH_CODE, 404);
        }
        $auth_code = $request->input('auth_code');
        if ($this->save_authorizer_access_token($auth_code)) {
            return view('notice', ['notice' => '成功绑定微信平台', 'url' => route('web.wechat.index'),'page'=>'微信平台列表']);
        } else {
            return view('notice', ['notice' => '出错啦，请稍后再绑定吧！', 'url' => route('web.wechat.index'),'page'=>'微信平台列表']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $platform = WechatPlatform::findOrFail($id);
        if ($platform->owner_id != Auth::user()->id) {
            return response()->json(['status'=>403,'无权操作']);
        }
        $platform->delete();
        return response()->json(0);
    }

    /**
     * 推送component_verify_ticket协议
     * 在公众号第三方平台创建审核通过后，微信服务器会向其“授权事件接收URL”每隔10分钟定时推送component_verify_ticket。第三方平台方在收到ticket推送后也需进行解密（详细请见【消息加解密接入指引】），接收到后必须直接返回字符串success。
     * POST数据示例
     * 存在redis里'ticket'
     * <xml>
     * <AppId> </AppId>
     * <CreateTime>1413192605 </CreateTime>
     * <InfoType> </InfoType>
     * <ComponentVerifyTicket> </ComponentVerifyTicket>
     * </xml>
     * @return bool|string
     */
    public function auth(Request $request)
    {
        //构造参数
        $token = $this->component_token;
        $encodingAesKey = $this->component_key;
        $appId = $this->component_appid;

        $wxbizmsgcrypt = new Wxbizmsgcrypt();
        $wxbizmsgcrypt->set($token, $encodingAesKey, $appId);

        //收到消息解密
        $msg = '';
        $msg_sign = $request->input('msg_signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');
        $postData = file_get_contents('php://input');
        $errCode = $wxbizmsgcrypt->decryptMsg($msg_sign, $timestamp, $nonce, $postData, $msg);
        Log::info('推送component_verify_ticket协议-错误码: ' . $errCode);
        if ($errCode == 0) {
            //解析XML
            $msgobj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!empty($msgobj->ComponentVerifyTicket)) {
                $ticket = (string)$msgobj->ComponentVerifyTicket;
                //存入redis
                Cache::put('ticket', $ticket, 10);
            };
        }
        return response('success');
    }

    /**
     * 微信平台消息接受URL
     * @param string $appid 公众号APPID
     * @return bool
     */
    public function callback(Request $request, $appid = '')
    {
        $this->appid = $appid;

        //构造参数
        $token = $this->component_token;
        $encodingAesKey = $this->component_key;
        $appId = $this->component_appid;

        $wxbizmsgcrypt = new Wxbizmsgcrypt();
        $wxbizmsgcrypt->set($token, $encodingAesKey, $appId);

        //收到消息解密
        $msg = '';
        $msg_sign = $request->input('msg_signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');
        $postData = file_get_contents('php://input');
        $errCode = $wxbizmsgcrypt->decryptMsg($msg_sign, $timestamp, $nonce, $postData, $msg);
        if ($errCode == 0) {
            if (!empty($msg)) {
                Log::info('微信消息：' . $msg);
                $postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);
                $result = '';
                if ($RX_TYPE == 'text') {
                    $result = $this->receiveText($postObj);
                }
                elseif ($RX_TYPE=='event' && $postObj->Event=='subscribe'){
                    //如果是用户关注,删除缓存
                    $openid=$postObj->FromUserName;
                    Cache::forget(RedisKey::userInfoKey($openid));
                }
                $encryptMsg = '';
                $errCode = $wxbizmsgcrypt->encryptMsg($result, $timestamp, $nonce, $encryptMsg);
                if ($errCode == 0) {
                    return response($encryptMsg);
                } else {
                    return response();
                }
            } else {
                return response();
            }
        } else {
            return response();
        }
    }

    //接收文本消息
    private function receiveText($object, $Recognition = "")
    {
        //分析接收的文本
        $keyword = $Recognition ? trim($Recognition) : trim($object->Content);

        //回复内容预设为空
        $content = "";

        if ($content) {
            if (is_array($content)) {
                if (isset($content[0])) {
                    $result = $this->transmitNews($object, $content);
                }
            } else {
                $result = $this->transmitText($object, $content);
            }
            return $result;
        }

    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
					    <ToUserName><![CDATA[%s]]></ToUserName>
					    <FromUserName><![CDATA[%s]]></FromUserName>
					    <CreateTime>%s</CreateTime>
					    <MsgType><![CDATA[text]]></MsgType>
					    <Content><![CDATA[%s]]></Content>
					</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if (!is_array($newsArray)) {
            return;
        }
        $itemTpl = "<item>
			            <Title><![CDATA[%s]]></Title>
			            <Description><![CDATA[%s]]></Description>
			            <PicUrl><![CDATA[%s]]></PicUrl>
			            <Url><![CDATA[%s]]></Url>
			        </item>";
        $item_str = "";
        foreach ($newsArray as $item) {
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
					    <ToUserName><![CDATA[%s]]></ToUserName>
					    <FromUserName><![CDATA[%s]]></FromUserName>
					    <CreateTime>%s</CreateTime>
					    <MsgType><![CDATA[news]]></MsgType>
					    <ArticleCount>%s</ArticleCount>
					    <Articles>$item_str</Articles>
					</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    /**
     * 获取第三方平台component_access_token
     * 第三方平台compoment_access_token是第三方平台的下文中接口的调用凭据，也叫做令牌（component_access_token）。
     * 如果缓存里有就直接取，否则调用api获取
     * 每个令牌是存在有效期（2小时）的，且令牌的调用不是无限制的，请第三方平台做好令牌的管理，在令牌快过期时（比如1小时50分）再进行刷新。
     */
    public function get_api_component_token()
    {
        //如果是阿里云主机，那么从腾讯主机获取token
        if (gethostname() == 'VM_121_124_centos' || gethostname()=='ly-pc') {
            return cURL::get('http://form.meizucampus.com/web/wechat/api_component_token');
        }
        $component_access_token = Cache::get('component_access_token');
        if (empty($component_access_token)) {
            $ticket = Cache::get('ticket');
            if (empty($ticket)) {
                throw new WechatException(WechatException::NO_TICKET);
            }
            $posturl = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
            $post_data['component_appid'] = $this->component_appid;
            $post_data['component_appsecret'] = $this->component_appsecret;
            $post_data['component_verify_ticket'] = $ticket;

            $response = cURL::jsonPost($posturl, $post_data);
            if ($response->statusCode != 200) {
                throw new WechatException(WechatException::POST_ERROR);
            }
            $responseObj = json_decode($response->body);
            if (empty($responseObj->component_access_token)) {
                throw new WechatException($responseObj->errmsg);
            }
            $component_access_token = $responseObj->component_access_token;
            $expires_in = $responseObj->expires_in;
            Cache::put('component_access_token', $component_access_token, $expires_in / 60);
            return $component_access_token;
        }
        return Cache::get('component_access_token');
    }
    /**
     * 通过腾讯主机获得token
     */
    public function api_component_token(){
        if (gethostname() == 'iZ2398ajniwZ') {
            return response($token = $this->get_api_component_token());
        }
    }

    /**
     * 获取预授权码pre_auth_code
     */
    public function get_pre_auth_code()
    {
        $token = $this->get_api_component_token();
        $component_appid = $this->component_appid;
        $posturl = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=$token";
        $post_data['component_appid'] = $component_appid;

        $return_json = cURL::jsonPost($posturl, $post_data)->body;
        Log::info($return_json);
        $pre_auth_code = json_decode($return_json)->pre_auth_code;
        $expires_in = intval(json_decode($return_json)->expires_in);
        Cache::put('pre_auth_code', $pre_auth_code, $expires_in / 60);
        return $pre_auth_code;
    }

    /**
     * 使用授权码换取公众号的接口调用凭据和授权信息
     * 该API用于使用授权码换取授权公众号的授权信息，并换取authorizer_access_token和authorizer_refresh_token。
     * 授权码的获取，需要在用户在第三方平台授权页中完成授权流程后，在回调URI中通过URL参数提供给第三方平台方。
     * 请注意，由于现在公众号可以自定义选择部分权限授权给第三方平台，因此第三方平台开发者需要通过该接口来获取公众号具体授权了哪些权限，而不是简单地认为自己声明的权限就是公众号授权的权限。
     * @return
     * @throws WechatException
     * @internal param string $auth_code_value 授权code,会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
     * @internal param string $appid 第三方平台appid
     * @internal param $token
     */
    public function get_authorizer_access_token()
    {
        $authorizer_access_token = Cache::get('authorizer_access_token_' . $this->appid);
        if (empty($authorizer_access_token)) {
            $component_appid = $this->component_appid;
            $token = $this->get_api_component_token();
            $result = WechatPlatform::where('appid', $this->appid)->first();
            if ($result) {
                //如果获取到刷新令牌，则获取（刷新）授权公众号的接口调用凭据（令牌）
                $refresh_token = $result->authorizer_refresh_token;
                return $this->refresh_authorizer($component_appid, $refresh_token, $token);
            } else {
                throw new WechatException(WechatException::NO_TOKEN);
            }
        }
        return $authorizer_access_token;
    }

    protected function save_authorizer_access_token($auth_code)
    {
        $appid = $this->component_appid;
        $token = $this->get_api_component_token();
        $posturl = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=$token";
        $post_data['component_appid'] = $appid;
        $post_data['authorization_code'] = $auth_code;

        $return_json = cURL::jsonPost($posturl, $post_data)->body;
        Log::info('$return_json' . $return_json);
        $return = json_decode($return_json);
        $auth_appid = $return->authorization_info->authorizer_appid;
        $authorizer_access_token = $return->authorization_info->authorizer_access_token;
        $expires_in = intval($return->authorization_info->expires_in);
        $authorizer_refresh_token = $return->authorization_info->authorizer_refresh_token;
        $func_info = $return->authorization_info->func_info;

        //保存authorizer_refresh_token到数据库
        $wechatPlatform = WechatPlatform::where('appid', $auth_appid)->first();
        if (!$wechatPlatform) {
            $wechatPlatform = new WechatPlatform();
        }
        $wechatPlatform->appid = $auth_appid;
        $wechatPlatform->authorizer_refresh_token = $authorizer_refresh_token;
        $funcs = [];
        foreach ($func_info as $item) {
            $funcs[$item->funcscope_category->id] = WechatPlatform::$func[$item->funcscope_category->id];
        }
        $wechatPlatform->func_info = $funcs;
        $wechatPlatform->owner_id = Auth::user()->id;
        $authorizer_info = $this->get_authorizer_info($auth_appid);
        $wechatPlatform->nick_name = $authorizer_info->nick_name;
        $wechatPlatform->head_img = $authorizer_info->head_img;
        $wechatPlatform->service_type_info = $authorizer_info->service_type_info->id;
        $wechatPlatform->verify_type_info = $authorizer_info->verify_type_info->id;
        $wechatPlatform->user_name = $authorizer_info->user_name;
        $wechatPlatform->principal_name = $authorizer_info->principal_name;
        $wechatPlatform->alias = $authorizer_info->alias;
        $qrcode='qrcode/'.$auth_appid.'.jpg';
        $qrcode_url=url(Storage::url($qrcode));;
        Storage::put($qrcode,file_get_contents($authorizer_info->qrcode_url));
        $wechatPlatform->qrcode_url = $qrcode_url;

        if ($wechatPlatform->save()) {
            Cache::put('authorizer_access_token_' . $auth_appid, $authorizer_access_token, $expires_in / 60);
            return true;
        }
        return false;
    }

    /**
     * 获取（刷新）授权公众号的接口调用凭据（令牌）
     * 该API用于在授权方令牌（authorizer_access_token）失效时，可用刷新令牌（authorizer_refresh_token）获取新的令牌。
     * @param $component_appid string 第三方平台appid
     * @param $refresh_token string 授权方的刷新令牌，刷新令牌主要用于公众号第三方平台获取和刷新已授权用户的access_token，只会在授权时刻提供，请妥善保存。一旦丢失，只能让用户重新授权，才能再次拿到新的刷新令牌
     * @param $component_access_token
     * @return
     * @internal param string $auth_appid 授权方appid
     */
    public function refresh_authorizer($component_appid, $refresh_token, $component_access_token)
    {
        $posturl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=$component_access_token";
        $post_data['component_appid'] = $component_appid;
        $post_data['authorizer_appid'] = $this->appid;
        $post_data['authorizer_refresh_token'] = $refresh_token;

        $return_json = cURL::jsonPost($posturl, $post_data)->body;
        $result = json_decode($return_json);
        $authorizer_access_token = $result->authorizer_access_token;
        $expires_in = intval($result->expires_in);
        Cache::put('authorizer_access_token_' . $this->appid, $authorizer_access_token, $expires_in / 60);
        return $authorizer_access_token;
    }

    /**
     * 获取授权方的公众号帐号基本信息
     * 该API用于获取授权方的公众号基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL。
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=002b13e33327cff5d22698934b4403f62b8d8329&lang=zh_CN
     * @param $appid
     * @param $auth_appid
     * @param $token
     */
    public function get_authorizer_info($auth_appid)
    {
        $token = $this->get_api_component_token();
        $posturl = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=$token";
        $post_data['component_appid'] = $this->component_appid;
        $post_data['authorizer_appid'] = $auth_appid;

        $return_json = cURL::jsonPost($posturl, $post_data)->body;
        return json_decode($return_json)->authorizer_info;
    }


    /**
     * 微信端登陆时获取用户openid
     * @param Request $request
     * @return string openid
     * @throws WechatException
     */
    public function getOpenid(Request $request, $field = self::OPENID, $appid = '')
    {
        if (!isWechat()) {
            return view('notice', ['notice' => '请通过微信访问', 'qrcode' => true]);
        }
        //默认使用env里面的appid
        if (empty($appid)) {
            if ($request->session()->has('appid')) {
                $appid=$request->session()->get('appid');
            }else {
                $appid = env('DEFAULT_APPID');
            }
        }
        if ($request->session()->has($appid . $field)) {
            return $request->session()->get($appid . $field);
        }

        //如果没有state参数那么重定向
        $weixinState = $request->input('state');
        if (empty($weixinState)) {
            $add_url = $request->url();
            $component_appid = env('COMPONENT_APPID');
            $redirectUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=" . urlencode($add_url) . "&response_type=code&scope=snsapi_userinfo&state=STATE&component_appid=$component_appid#wechat_redirect";
            header("location: $redirectUrl");
            exit;
        }
        $appid = $request->input('appid');
        $code = $request->input('code');
        if (empty($code) || empty($appid)) {
            throw new WechatException(WechatException::NO_AUTH_CODE);
        }
        $component_appid = $this->component_appid;
        $component_access_token = $this->get_api_component_token();
        $url = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=$appid&code=$code&grant_type=authorization_code&component_appid=$component_appid&component_access_token=$component_access_token";
        $str = cURL::get($url);
        Log::debug('getOpenid：' . $str);
        $json_arr = json_decode($str, true);

        if (empty($json_arr['openid'])) {
            throw new WechatException(WechatException::NO_OPENID);
        }
        if (empty($json_arr['unionid'])) {
            throw new WechatException(WechatException::NO_UNIONID);
        }
        Cache::put($appid . '_access_token', $json_arr['access_token'], $json_arr['expires_in'] / 60);
        Cache::put($appid . '_refresh_token', $json_arr['refresh_token'], 30 * 24 * 60);
        $request->session()->put($appid . 'openid', $json_arr['openid']);
        $request->session()->put($appid . 'unionid', $json_arr['unionid']);
        if (!Cache::has($json_arr['unionid'])) {
            //存用户微信信息入缓存
            //https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318590&token=564fdd8d71d0dbe74595555d3fec59d61e1ac0fd&lang=zh_CN
            $userinfo = cURL::get("https://api.weixin.qq.com/sns/userinfo?access_token={$json_arr['access_token']}&openid={$json_arr['openid']}&lang=zh_CN");
            Cache::put($json_arr['unionid'], $userinfo, 24 * 60);
        }
        return $json_arr[$field];
    }


    /**
     * 发送微信通知
     * @param $platformAppid 平台Appid
     * @param $templateId 模板ID
     * @param $msgData
     * @param string $openid 用户openid
     * @return bool
     */
    public function send_message($msgData)
    {
        //获取AUTHORIZER_ACCESS_TOKEN
        $access_token = $this->get_authorizer_access_token();
        //发送消息通知
        $posturl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";

        $return_json = cURL::jsonPost($posturl, $msgData)->body;
        Log::debug('发送模板消息返回：' . $return_json);
        if (json_decode($return_json)->errcode == 0) {
            return true;
        }//发送成功
        return false;//发送失败
    }

    public function send_news($openid, $title, $description, $url, $picurl)
    {
        //发送图文
        $access_token = $this->get_authorizer_access_token();
        $news_data['touser'] = $openid;
        $news_data['msgtype'] = 'news';
        $news_data['news'] = array(
            'articles' => array(
                array(
                    'title' => urlencode($title),
                    "description" => urlencode($description),
                    "url" => urlencode($url),
                    "picurl" => urlencode($picurl)
                )
            )
        );
        //$news_data = urldecode(json_encode($news_data));
        $news_posturl = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        $news_return_json = cURL::jsonPost($news_posturl, $news_data)->body;
        $news_return_obj = json_decode($news_return_json);
        if ($news_return_obj->errcode == 0) {
            return true;
        }
        return false;
    }

    public function is_subscribe($openid)
    {
        $subscribe = $this->getUserInfo($openid, 'subscribe');
        if ($subscribe == 1) {
            return true;
        }
        return false;
    }

    public function getUserInfo($openid, $field = false)
    {
        $get_obj = Cache::remember(RedisKey::userInfoKey($openid), self::CACHE_MONTH, function() use ($openid) {
            $access_token = $this->get_authorizer_access_token();
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
            $get_json = cURL::get($url);
            Log::info('getUserInfo:'.$get_json);
            $get_obj = json_decode($get_json, true);
            return $get_obj;
        });
        if ($field !== false && isset($get_obj[$field])) {
            return $get_obj[$field];
        } else {
            return $get_obj;
        }
    }
}
