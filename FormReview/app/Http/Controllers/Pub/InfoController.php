<?php

namespace App\Http\Controllers\Pub;

use anlutro\cURL\Laravel\cURL;
use App\Exceptions\WechatException;
use App\Form;
use App\FormAdmin;
use App\FormData;
use App\Forms\CustomForm;
use App\Http\Controllers\Web\WechatController;
use App\Notice;
use App\User;
use App\Wechat\WechatPlatform;
use Exception;
use Illuminate\Http\Request;

use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;

class InfoController extends Controller
{
    const SUBSCRIBE_RETURN = 1;
    const BEYONDLIMIT_RETURN = 2;

    protected function showForm(Request $request, $fid, $formData = null)
    {

        $formTemplate = Form::undelete()->findOrFail($fid);
        if ($formTemplate->publish == 0 && (Auth::guest() || !Auth::user()->forms->contains($formTemplate))) {
            abort(404);
        }
        //是否排除黑名单
        if ($formTemplate->filterBlacklist) {
            //$ip = $request->ip();
            //TODO 拉黑，全局？局部？
            //abort(403, 'Unauthorized action.');
        }
        //获取微信openid
        if ($formData) {
            if (!empty($formData->canEdit) && $formTemplate->wechat) {
                //如果表单可以编辑，且用户是通过微信端填写，那么判断openid是否一致
                if (isWechat()) {
                    $platform_id = $formTemplate->wechat;
                    $appid = WechatPlatform::findOrFail($platform_id)->appid;
                    $wechat = new WechatController($appid);
                    $openid = $wechat->getOpenid($request);
                    if (empty($formData->openid) || $openid != $formData->openid) {
                        $formData->canEdit = false;
                    }
                } else {
                    $formData->canEdit = false;
                }
            }
        } else {
            if($this->beyondLimit($formTemplate, 'ip',$request->ip())){
                return self::BEYONDLIMIT_RETURN;
            }
            if ($formTemplate->wechat) {
                //是否通过微信打开？
                if (isWechat()) {
                    $platform_id = $formTemplate->wechat;
                    $platform = WechatPlatform::findOrFail($platform_id);
                    $request->session()->put('appid', $platform->appid);

                    $wechat = new WechatController($platform->appid);
                    $openid = $wechat->getOpenid($request);
                    if (!$wechat->is_subscribe($openid)) {
                        return $platform->qrcode_url;
                    }

                    if($this->beyondLimit($formTemplate, 'openid',$openid)){
                        return self::BEYONDLIMIT_RETURN;
                    }
                } else {
                    return false;
                }
            }
        }

        $url = route('pub.info.store', $fid);
        $data = [];
        if ($formData) {
            foreach (json_decode($formTemplate->fieldNames) as $field) {
                if (empty($field)) {
                    continue;
                }
                $data[$field] = $formData->$field;
            }
            $url = route('pub.info.update', $formData->id);
        }
        return [
            'fieldsData' => $formTemplate->fields,
            'fid' => $formTemplate->id,
            'data' => json_encode($data),
            'url' => $url,
            'form' => $formTemplate,
            'customView' => $formTemplate->customView,
            'customStatus' => $formTemplate->customStatus,
        ];
    }

    protected function saveForm(Request $request, $fid, $formData = null)
    {
        $formTemplate = Form::publish()->findOrFail($fid);

        //TODO 后台验证
        $this->validate($request, [
//            'title' => 'required|unique:posts|max:255',
//            'body' => 'required',
        ]);
        $isNew = false;//是否新建
        if ($formData === null) {
            $isNew = true;
            $formData = new FormData();
        }
        $formData->status = FormData::STATUS_INIT;
        $formData->fid = $fid;
        $formData->ip = $request->ip();
        //获取微信openid
        if ($formTemplate->wechat) {
            $wechat = new WechatController();
            $openid = $wechat->getOpenid($request);
            $formData->openid = $openid;
        }

        foreach (json_decode($formTemplate->fieldNames) as $field) {
            if (empty($field)) {
                continue;
            }
            //是否有文件？
            if ($request->hasFile($field)) {
                $fileName = $request->file($field)->getClientOriginalName();
                preg_match('/\.\w+$/', $fileName, $matchs);
                $file = 'files/' . uniqid() . $matchs[0];
                $formData->$field = url(Storage::url($file));
                Storage::put(
                    $file,
                    file_get_contents($request->file($field)->getRealPath()),
                    'public'
                );
            } else {
                $formData->$field = $request->input($field);
            }
        }
        if (!Auth::guest()) {
            $formData->user_id = Auth::user()->id;
        }

        //判断是否超过限制次数
        if ($isNew && $formTemplate->limitTimes>=0) {
            $limitBy = $formTemplate->limitBy;
            $count = FormData::where('fid',$fid)->where($limitBy,$formData->$limitBy)->count();
            if ($count >= $formTemplate->limitTimes) {
                return false;
            }
        }

        $formData->canEdit = true;
        $formData->deleted_at = null;
        $formData->save();

        //通知管理员
        $this->tellAdmin($fid, $formData);

        return $formData->id;
    }

    public function create(Request $request, $fid)
    {
        $data = $this->showForm($request, $fid);
        if ($data == self::BEYONDLIMIT_RETURN) {
            return view('notice', ['notice' => '您已提交过表单，请勿再次提交', 'error' => true]);
        }
        if (is_string($data) && strpos($data,'http:')!==-1) {
            return view('notice', ['notice' => '请先关注此公众号', 'qrcode' => $data]);
        }
        if (is_array($data)) {
            if ($data['customStatus'] == Form::REVIEW_PASS && !empty($data['customView'])) {
                $data['customView'] = preg_replace('/(<form[\s\S]*?>)/i',
                    '$1' . csrf_field(), $data['customView']);
                return response($data['customView']);
            }
            return view('pub.info.info')->with($data);
        }
        return view('notice', ['notice' => '请通过微信访问', 'qrcode' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $fid)
    {
        $formDataId = $this->saveForm($request, $fid);
        if (!$formDataId) {
            return view('notice', ['notice' => '已超出填写限制次数', 'error' => true]);
        }
        return view('notice', [
            'notice' => '提交成功！',
            'url' => route('pub.info.show', $formDataId),
            'page' => '查看'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $formData = FormData::findOrFail($id);
        $form = $formData->form;
        $data = $this->showForm($request, $form->id, $formData);
        if (is_array($data)) {
            if (empty($formData->canEdit) || !$formData->canEdit) {
                $data['canEdit'] = false;
            }
            $data['status'] = $formData->status;

            $form->showReview && $data['reviews'] = $formData->reviews;
            $form->showRemark && $data['remarks'] = $formData->remarks;

            //是否为自定义页面
            //由于自定义页面js可能发生错误，无法自动填充，所以暂时使用普通页面
            if (false && $data['customStatus'] == Form::REVIEW_PASS && !empty($data['customView'])) {
                $add='<div id="fields" data-value="'.$data['fieldsData'].'"></div><script src="/assets/js/jquery.formautofill.min.js"></script><script src="/assets/js/customview.js"></script>';
                $data['customView'] = preg_replace('/(<form[\s\S]*?>)/i',
                    '$1' . csrf_field(), $data['customView']);
                $data['customView'] = preg_replace('/(<\/form>)/i',
                    '$1' . $add, $data['customView']);
                return response($data['customView']);
            }
            return view('pub.info.info', $data);
        }
        return view('notice', ['notice' => '请通过微信访问', 'qrcode' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return redirect()->route('pub.info.show', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //TODO 判断是否为填写者
        $formData = FormData::findOrFail($id);
        if (!$formData->canEdit) {
            return view('notice', [
                'notice' => '您不能修改',
                'url' => route('pub.info.show', $formData->id),
                'page' => '查看'
            ]);
        }
        $formDataId = $this->saveForm($request, $formData->fid, $formData);
        return view('notice', [
            'notice' => '提交成功！',
            'url' => route('pub.info.show', $formDataId),
            'page' => '查看'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $formData = FormData::findOrFail($id);
        $formData->delete();
    }

    /**
     * 通知管理员
     * @param $fid
     * @param $formData
     */
    protected function tellAdmin($fid, $formData)
    {
        $notice = Notice::where([
            'form_id' => (string)$fid,
            'type' => (string)Notice::TYPE_ADMIN,
            'way' => (string)Notice::WAY_WECHAT,
            'status' => true
        ])->first();
        if ($notice) {
            $platformId = $notice->data['platform_id'];
            $platform = WechatPlatform::find($platformId);
            $openids = $this->getAdminOpenids($fid, $formData);
            Log::debug('getAdminOpenids:' . json_encode($openids));
            if (!empty($platform) && !empty($openids)) {
                foreach ($openids as $openid) {
                    $msgData = $notice->data['review'][1]['pass'];//暂定这个，为了和审核模板结构一致
                    $msgData['touser'] = $openid;
                    $msgData['template_id'] = $notice->data['template_id'];
                    if ($msgData['url'] == Notice::CONTENT_USER_LINK) {
                        $msgData['url'] = route('pub.info.show', $formData->id);//指向用户填写的表单
                    }
                    $wechat = new WechatController($platform->appid);
                    //替换{name}——内容
                    foreach ($msgData['data'] as $k => $keys) {
                        //增加微信昵称查找
                        if (mb_strpos($keys['value'], Notice::CONTENT_NICKNAME) !== false && !empty($formData->openid)) {
                            $nickname = $wechat->getUserInfo($formData->openid, 'nickname');
                            $keys['value'] = str_replace(Notice::CONTENT_NICKNAME, $nickname,
                                $keys['value']);
                        }
                        if (mb_strpos($keys['value'], Notice::CONTENT_TIME) !== false) {
                            $keys['value'] = str_replace(Notice::CONTENT_TIME, date('Y-m-d H:i:s'),
                                $keys['value']);
                        }

                        preg_match_all('/{(.*?)}/', $keys['value'], $matches);
                        if (!empty($matches[1])) {
                            foreach ($matches[1] as $match) {
                                $value = $formData->$match;
                                if ($match == 'status') {
                                    $value=status2zh($value);
                                }
                                $keys['value'] = str_replace('{' . $match . '}', $value, $keys['value']);
                            }
                        }
                        $keys['value'] = str_replace(Notice::CONTENT_BR, "\n",
                            $keys['value']);
                        $msgData['data'][$k]['value'] = $keys['value'];
                    }
                    try {
                        Log::info('通知管理员：'.json_encode($msgData));
                        $wechat->send_message($msgData);
                    } catch (Exception $e) {
                        Log::debug($e->getMessage());
                    }
                }
            } else {
                Log::debug('通知管理员：没有找到微信平台或者用户不是通过微信提交表单，无法发送微信通知！\'');
            }
        }
    }

    /**
     * 查找管理员openid
     * @param $fid
     * @param $formData
     * @return array
     */
    public function getAdminOpenids($fid, $formData)
    {
        $result=[];
        $admins = FormAdmin::where('form_id', $fid)->get();
        foreach ($admins as $admin) {
            //如果表单状态和管理员所管理的状态不符，那么跳过
            if(!in_array($formData->status,json_decode($admin->stage))){
                continue;
            }
            $conditions = json_decode($admin->conditions);
            foreach ($conditions as $condition) {
                $whereIn = explode(',', $condition->value);
                $key=$condition->key;
                //如果不满足筛选条件，那么跳过
                if(!in_array($formData->$key, $whereIn)){
                    continue;
                }
            }
            $user = $admin->user;
            empty($user->openid) || $result[]=$user->openid;
        }
        return $result;
    }

    private function beyondLimit($formTemplate, $limitBy, $value)
    {
        if ($formTemplate->limitTimes>=0 && $formTemplate->limitBy==$limitBy) {
            $count = FormData::where('fid',(string)$formTemplate->id)->where($limitBy,$value)->count();
            if ($count >= $formTemplate->limitTimes) {
                return true;
            }
        }
        return false;
    }
}
