<?php

namespace App\Http\Controllers\Web;

use App\Form;
use App\FormAdmin;
use App\FormData;
use App\Notice;
use App\Remark;
use App\Review;
use App\Wechat\WechatPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;
use phpDocumentor\Reflection\Types\Integer;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {

    }

    protected function createForm($formTemplate, $url, $method = 'POST', $model = null)
    {
        $fields = json_decode($formTemplate->fields);
        $form = $this->form(CustomForm::class, [
            'method' => $method,
            'url' => $url,
            'class' => 'form-horizontal',
            'model' => $model,
        ]);
        foreach ($fields as $field) {
            $form->add($field->name, $field->type, [
                'label' => $field->label,
                'rules' => $field->validate,
                'help_block' => [
                    'text' => $field->helpText,
                    'tag' => 'p',
                    'attr' => ['class' => 'help-block']
                ],
                'errors' => ['class' => 'text-danger'],
            ]);
        }
        $form->add('submit', 'submit', [
            'wrapper' => ['class' => 'form-group'],
            'attr' => ['class' => 'form-control col-sm-2'],
            'label' => '提交'
        ]);
        $form->add('fid', 'hidden', ['default_value' => $formTemplate->id]);
        return $form;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'fid' => 'required',
            'fd_id' => 'required',
            'result' => 'required',
            'evaluation' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json(['status' => 2, 'error' => $v->errors()]);
        }

        $form_data_id = $request->input('fd_id');
        $formData = FormData::findOrFail($form_data_id);
        $result = intval($request->input('result'));

        $fid = $request->input('fid');
        $form = Form::find($fid);
        if (!$form) {
            return response()->json(['status' => 1, 'error' => '404']);
        }
        //是否有权限审核？
        $this->authorize('review', $form);

        $formAdmin = FormAdmin::where('user_id', Auth::user()->id)->where('form_id', $form->id)->accepted()->first();
        $stages = $formAdmin->stage ? json_decode($formAdmin->stage) : [];
        $stages = collect($stages)->map(function ($item, $key) {
            return intval($item);
        });
        if(!in_array($formData->status, $stages->toArray())){
            return response()->json(['status' => 1, 'error' => '您没有权限审批哦']);
        }

        if ($formData->status >= $form->reviewTimes) {
            return response()->json(['status' => 1, 'error' => '已经到终审']);
        }

        //发送通知
        $notice = Notice::where([
            'form_id' => $fid,
            'type' => (string)Notice::TYPE_REVIEW,
            'way' => (string)Notice::WAY_WECHAT,
            'status' => true
        ])->first();
        if ($notice) {
            $platformId = $notice->data['platform_id'];
            $platform = WechatPlatform::find($platformId);
            if (!empty($platform) && !empty($formData->openid)) {
                $msgData = $notice->data['review'][$formData->status + 1][$result == 1 ? 'pass' : 'refuse'];
                $msgData['touser'] = $formData->openid;
                $msgData['template_id'] = $notice->data['template_id'];
                if ($msgData['url'] == Notice::CONTENT_USER_LINK) {
                    $msgData['url'] = route('pub.info.show', $form_data_id);//指向用户填写的表单
                }
                $wechat = new WechatController($platform->appid);

                foreach ($msgData['data'] as $k => $keys) {
                    //替换{NICKNAME}——微信昵称查找
                    //TODO 加缓存
                    if (mb_strpos($keys['value'], Notice::CONTENT_NICKNAME) !== false) {
                        $nickname = $wechat->getUserInfo($formData->openid, 'nickname');
                        $keys['value'] = str_replace(Notice::CONTENT_NICKNAME, $nickname,
                            $keys['value']);
                    }
                    //替换{EVALUATION}——审核内容
                    if (mb_strpos($keys['value'], Notice::CONTENT_EVALUATION) !== false) {
                        $keys['value'] = str_replace(Notice::CONTENT_EVALUATION, $request->input('evaluation'),
                            $keys['value']);
                    }
                    //替换{CUSTOM}——自定义查找
                    if (mb_strpos($keys['value'], Notice::CONTENT_CUSTOM) !== false) {
                        $wechatNoticeArr = $request->input("wechatNotice");
                        $keys['value'] = str_replace(Notice::CONTENT_CUSTOM, $wechatNoticeArr[$k], $keys['value']);
                    }

                    preg_match_all('/{(.*?)}/', $keys['value'], $matches);
                    if (!empty($matches[1])) {
                        foreach ($matches[1] as $match) {
                            $value = $formData->$match;
                            if ($match == 'status') {
                                $value = status2zh(intval($value) + 1);
                            }
                            $keys['value'] = str_replace('{' . $match . '}', $value, $keys['value']);
                        }
                    }
                    $keys['value'] = str_replace(Notice::CONTENT_BR, "\n",
                        $keys['value']);
                    $msgData['data'][$k]['value'] = $keys['value'];

                }
                try {
                    if (!$wechat->send_message($msgData)) {
                        return response()->json(['status' => 1, 'error' => ' 发送微信通知失败']);
                    }
                } catch (Exception $e) {
                    return response()->json(['status' => 1, 'error' => $e->getMessage() . ' 无法发送微信通知']);
                }
            } else {
                return response()->json(['status' => 1, 'error' => '没有找到微信平台或者用户不是通过微信提交表单，无法发送微信通知！']);
            }
        }
        $review = new Review();
        $review->form_data_id = $form_data_id;
        $review->user_id = Auth::user()->id;
        $review->status = $formData->status;
        $review->result = $result;
        $review->evaluation = $request->input('evaluation');
        $review->remark = $request->input('remark');
        if ($review->save()) {
            $formData->canEdit = false;
            $formData->status = ($formData->status + 1) * $result;
            $formData->save();

            return response()->json(['status' => 0]);
        };
        return response()->json(['status' => 1]);
    }

    public function remark(Request $request)
    {
        $v = Validator::make($request->all(), [
            'fd_id' => 'required',
            'remark' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json(['status' => 2, 'errors' => $v->errors()]);
        }

        $form_data_id = $request->input('fd_id');

        //是否有权限评论？
        $form_id = intval($request->input('fid'));
        $this->authorize('remark', Form::findOrFail($form_id));


        $review = new Remark();
        $review->form_data_id = $form_data_id;
        $review->user_id = Auth::user()->id;
        $review->remark = $request->input('remark');
        if ($review->save()) {
            return response()->json(['status' => 0]);
        };
        return response()->json(['status' => 1]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('pub.info.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formData = FormData::findOrFail($id);
        $formTemplate = Form::publish()->undelete()->findOrFail($formData->fid);
        $url = route('pub.info.update', $id);
        $form = $this->createForm($formTemplate, $url, 'PUT', $formData);
        return view('pub.info.info', compact('form'));
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
        $formData = FormData::findOrFail($id);
        $formTemplate = Form::publish()->undelete()->findOrFail($formData->fid);
        $url = route('pub.info.update', $id);
        $form = $this->createForm($formTemplate, $url, 'PUT', $formData);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }
        $req = $form->getFieldValues();
        $formData->status = FormData::STATUS_INIT;
        foreach ($req as $key => $item) {
            $formData->$key = $req[$key];
        }
        $formData->ip = $request->ip();
        //获取微信openid
        if ($formTemplate->wechat) {
            $openid = WechatPlatform::get_openid($request);
            $formData->openid = $openid;
        }
        $formData->delete = false;
        $formData->save();
        echo '提交成功！<a href="' . url('/pub/info/' . $formData->id) . '">点击查看修改</a>';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getReviews(Request $request)
    {
        $formDataId = $request->input('fd_id');
        $formData = FormData::findOrFail($formDataId);
        $reviews = $formData->reviews;
        foreach ($reviews as $review) {
            $review->username = $review->user->name;
        }
        $remarks = $formData->remarks;
        foreach ($remarks as $remark) {
            $remark->username = $remark->user->name;
        }
        return response()->json(['reviews' => $reviews, 'remarks' => $remarks]);
    }
}
