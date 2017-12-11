<?php

namespace App\Http\Controllers\Web;

use App\FormAdmin;
use App\FormData;
use App\Notice;
use App\RedisKey;
use App\Review;
use App\User;
use App\Wechat\WechatPlatform;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Form;
use Illuminate\Support\Facades\Auth;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Kris\LaravelFormBuilder\FormBuilder;
use App\Forms\CreateForm;

class FormController extends Controller
{
    use FormBuilderTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $key = $request->input('key');
        return view('web/form/formlist')->withForms(Auth::user()->forms()->where('name', 'like', "%{$key}%")->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $platforms = $this->getPlatforms();
        return view('web.form.create')->with([
            'method' => 'POST',
            'url' => route('web.form.store'),
            'fieldsData' => '[]',
            'wechatPlatforms' => $platforms,
        ]);
    }


    /**
     * 预览
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(Request $request, $id = null)
    {
        if ($id) {
            $fields = Cache::get($id);
            if (!$fields) {
                abort(404);
            }
            $data = [
                'fieldsData' => $fields,
                'fid' => $id,
                'data' => json_encode([]),
                'url' => '',
            ];
            return view('pub.info.info')->with($data);
        } else {
            $tempId = uniqid();
            Cache::put($tempId, $request->input('fields'), 5);
            return response()->json(['status' => 0, 'id' => $tempId]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'reviewTimes' => 'required|max:10',
            'fields' => 'required',
            'fieldNames' => 'required',
        ]);
        $nform = new Form();
        $nform->name = $nform->title = $request->input('title');
        $nform->description = $request->input('form_description');
        if ($request->hasFile('pic')) {
            $pic = 'forms/' . uniqid('formpic');
            $nform->pic = Storage::url($pic);
            Storage::put(
                $pic,
                file_get_contents($request->file('pic')->getRealPath()),
                'public'
            );
        }
        $nform->creator = Auth::user()->id;
        $nform->publish = $request->input('publish') ? true : false;
        $nform->wechat = $request->input('wechat');
        $nform->filterBlacklist = $request->input('filterBlacklist') ? true : false;
        $nform->showReview = $request->input('showReview') ? true : false;
        $nform->showRemark = $request->input('showRemark') ? true : false;
        $nform->reviewTimes = $request->input('reviewTimes');
        $nform->fields = $request->input('fields');
        $nform->fieldNames = $request->input('fieldNames');
        $nform->save();

        $admin = new FormAdmin();
        $admin->form_id = $nform->id;
        $authorities = [];
        foreach (Form::$authorities as $authority => $value) {
            $authorities[] = $authority;
        }
        $admin->authorities = json_encode($authorities);
        $stages = [];
        $reviewTimes = intval($nform->reviewTimes);
        for ($i = 0; $i < $reviewTimes; $i++) {
            $stages[] = $i;
        }
        $admin->stage = json_encode($stages);
        $admin->conditions = json_encode([]);
        $admin->user_id = Auth::user()->id;
        $admin->inviter_id = Auth::user()->id;
        $admin->handle = FormAdmin::ACCEPT;
        $admin->save();

        return view('notice', ['notice' => '创建成功', 'url' => route('web.form.show', $nform->id), 'page' => '表单']);
    }

    /**
     * 概述
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $form = Form::findOrFail($id);
        //$this->authorize($form);//一个表单的首页，管理员必须有次权限

        $admin = FormAdmin::where('user_id', Auth::user()->id)->where('form_id', $id)->first();
        //有更新表单权限？
        $canUpdate = in_array('update', json_decode($admin->authorities));
        //筛选
        $conditions = json_decode($admin->conditions);
        $data = FormData::where('fid', $id);
        foreach ($conditions as $condition) {
            $whereIn = explode(',', $condition->value);
            $data = $data->whereIn($condition->key, $whereIn);
        }
        $temp = clone $data;
        //今日提交
        $summary['todayCount'] = $temp->where('updated_at', '>=', new \DateTime('today'))->count();

        $data = $data->get();

        //审核阶段筛选
        $stages = $admin->stage ? json_decode($admin->stage) : [];
        $stages = collect($stages)->map(function ($item, $key) {
            return intval($item);
        });
        $noReview = $data->filter(function ($item) use ($stages) {
            return in_array($item->status, $stages->toArray());
        });
        $summary['noReviewCount'] = $noReview->count();

        $hasReviewStatus = $stages->map(function ($item, $key) {
            return -$item - 1;
        });
        $hasReviewStatus->push($stages->max() + 1);
        $hasReview = $data->filter(function ($item) use ($hasReviewStatus) {
            return in_array($item->status, $hasReviewStatus->toArray());
        });
        $summary['hasReviewCount'] = $hasReview->count();

        //总数据
        $summary['totalCount'] = $summary['noReviewCount'] + $summary['hasReviewCount'];

        return view('web/form/summary')->withForm($form)->with(['summary' => $summary, 'canUpdate' => $canUpdate]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);
        $platforms = $this->getPlatforms();
        return view('web.form.create')->with([
            'method' => 'PUT',
            'url' => route('web.form.update', $id),
            'form' => $form,
            'wechatPlatforms' => $platforms,
        ]);
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
        $this->validate($request, [
            'title' => 'required|max:255',
            'reviewTimes' => 'required|max:10',
            'fields' => 'required',
            'fieldNames' => 'required',
        ]);
        $nform = Form::findOrFail($id);
        $this->authorize($nform);
        $nform->name = $nform->title = $request->input('title');
        $nform->description = $request->input('form_description');
        if ($request->hasFile('pic')) {
            $pic = 'forms/' . $request->file('pic')->hashName();
            $nform->pic = url(Storage::url($pic));
            Storage::put(
                $pic,
                file_get_contents($request->file('pic')->getRealPath()),
                'public'
            );
        }
        $nform->creator = Auth::user()->id;
        $nform->publish = $request->input('publish') ? true : false;
        $nform->wechat = $request->input('wechat');
        $nform->filterBlacklist = $request->input('filterBlacklist') ? true : false;
        $nform->showReview = $request->input('showReview') ? true : false;
        $nform->showRemark = $request->input('showRemark') ? true : false;
        $nform->reviewTimes = $request->input('reviewTimes');
        $nform->fields = $request->input('fields');
        $nform->fieldNames = $request->input('fieldNames');
        $nform->save();

        return view('notice', ['notice' => '修改成功', 'url' => route('web.form.show', $nform->id), 'page' => '表单']);
    }

    /**
     * 概述页面ajax修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxUpdate(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $value = $request->input('value');
        if ($id && $name && $value) {
            $form = Form::find($id);
            if (!$form) {
                return response()->json(['status' => 1, 'error' => '表单不存在']);
            }
            $this->authorize('update', $form);

            if ($name == Form::CUSTOM_STATUS) {
                $form->$name = $value == 'true' ? Form::REVIEW_PASS : Form::CUSTOM_STOP;
            } else {
                $form->$name = $value == 'true' ? true : false;
            }

            if ($form->save()) {
                return response()->json(['status' => 0, 'error' => '']);
            }
        }
        return response()->json(['status' => 1, 'error' => '操作有误，请重试。']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('creator', $form);
        $form = Form::findOrFail($id);
        $form->delete();
        return response()->json(0);
    }

    /**
     * 非创建者解除表单管理
     */
    public function cancel($id)
    {
        $form=Form::findOrFail($id);
        $formAdmin = FormAdmin::where('user_id', Auth::user()->id)->where('form_id', $form->id)->accepted()->first();
        if ($formAdmin) {
            $formAdmin->delete();
            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => 1,'error'=>'无权限'],403);
    }

    public function datalist($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize($form);

        $columns = [];
        $columns[] = ['field' => 'state', 'checkbox' => 'true', 'align' => 'center'];
        $columns[] = ['field' => '_id', 'title' => 'ID', 'align' => 'center', 'sortable' => true];
        foreach (json_decode($form->fields) as $field) {
            if (empty($field->name)) {
                continue;
            }
            $columns[] = [
                'field' => $field->name,
                'title' => $field->label,
                'align' => 'center',
                'sortable' => true,
                'visible' => false,
                'filterControl' => "input"
            ];
        }
        $form->wechat && $columns[] = [
            'field' => 'openid',
            'title' => 'OPENID',
            'align' => 'center',
            'sortable' => true,
            'visible' => false,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'ip',
            'title' => 'IP',
            'align' => 'center',
            'sortable' => true,
            'visible' => false,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'updated_at',
            'title' => '更新时间',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'status',
            'title' => '状态',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "select"
        ];
        $columns[] = [
            'field' => 'operate',
            'title' => '操作',
            'align' => 'center',
            'events' => 'operateEvents',
            'formatter' => 'operateFormatter'
        ];
        return view('web.form.datalist',
            ['form' => $form, 'columns' => json_encode($columns), 'reviewTimes' => $form->reviewTimes]);
    }

    protected function getData(Request $request, $fid)
    {
//        $resultJson =  Cache::remember(RedisKey::formDataKey(),self::CACHE_MIN, function () use ($request,$fid) {
            $admin = FormAdmin::where('user_id', Auth::user()->id)->where('form_id', $fid)->first();
            $data = FormData::where('fid', $fid);

            if ($request->input('today')) {
                $data = $data->where('updated_at', '>=', new \DateTime('today'));
            }
            $conditions = json_decode($admin->conditions);
            foreach ($conditions as $condition) {
                $whereIn = explode(',', $condition->value);
                $data = $data->whereIn($condition->key, $whereIn);
            }

//        $limit = intval($request->input('limit'));
//        $offset = intval($request->input('offset'));
//        $order =$request->input('order');
//        $sort =$request->input('sort')?$request->input('sort'):'updated_at';
//        $search =$request->input('search')?$request->input('search'):'';
//        $limit < 1 && $limit = 1;
//        $offset < 0 && $offset = 0;
            $data = $data->get();

            //审核阶段筛选
            $stages = $admin->stage ? json_decode($admin->stage) : [];
            $stages = collect($stages)->map(function ($item, $key) {
                return intval($item);
            });
            $noReview = $data->filter(function ($item) use ($stages) {
                return in_array($item->status, $stages->toArray());
            });
            if ($request->input('noReview')) {
                return array_merge([],$noReview->all());
            }
            $hasReviewStatus = $stages->map(function ($item, $key) {
                return -$item - 1;
            });
            $hasReviewStatus->push($stages->max() + 1);
            $hasReview = $data->filter(function ($item) use ($hasReviewStatus) {
                return in_array($item->status, $hasReviewStatus->toArray());
            });
            if ($request->input('hasReview')) {
                return array_merge([],$hasReview->all());
            }
            $all = array_merge($noReview->all(), $hasReview->all());
            return $all;
//        });
//        return json_decode($resultJson);
    }

    //TODO 不要全部传给客户端
    public function dataData(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('datalist', $form);
        $data = $this->getData($request, $id);
        return response()->json($data);
    }


    /**
     * 通知设置
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function noticeSetting($id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('creator', $form);
        return view('web.form.noticeSetting', ['form' => $form]);
    }

    /**
     * 保存通知,form级别
     */
    public function saveNotice(Request $request, $fid)
    {
        //TODO 表单验证
        $form = Form::findOrFail($fid);
        $this->authorize('creator', $form);

        $type = $request->input('type');
        $way = $request->input('way');
        $status = $request->input('status') ? true : false;
        $notice = Notice::where(['form_id' => $fid, 'type' => $type, 'way' => $way])->first();
        if (empty($notice)) {
            $notice = new Notice();
        }
        $notice->type = $type;
        $notice->way = $way;
        $notice->status = $status;
        $notice->form_id = $fid;
        $data = [];
        if ($notice->way == Notice::WAY_WECHAT) {
            $data['platform_id'] = $form->wechat;
            $data['template_id'] = $request->input('template_id');
            $data['template_keys'] = explode(',', $request->input('template_keys'));
            for ($i = 1; $i <= $form->reviewTimes; $i++) {
                foreach ([Review::PASS, Review::REFUSE] as $r) {
                    $msg['url'] = $request->input('review_' . $i . '_' . $r . '_url');
                    $msg['topcolor'] = $request->input('review_' . $i . '_' . $r . '_topcolor');
                    foreach ($data['template_keys'] as $key) {
                        $msg['data'][$key]['color'] = $request->input('review_' . $i . '_' . $r . '_' . $key . '_color');
                        $msg['data'][$key]['value'] = $request->input('review_' . $i . '_' . $r . '_' . $key . '_value');
                    }
                    $data['review'][$i][$r] = $msg;
                }
            }
        }
        $notice->data = $data;
        if ($notice->save()) {
            return view('notice',
                ['notice' => '保存成功', 'url' => route('web.form.noticeSetting', $fid), 'page' => '通知设置']);
        } else {
            return view('notice', ['notice' => '保存失败']);
        }
    }

    public function getNotice(Request $request, $fid)
    {
        $form = Form::findOrFail($fid);
        $this->authorize('creator', $form);
        $type = $request->input('type');
        $way = $request->input('way');
        $notice = Notice::where(['form_id' => $fid, 'type' => $type, 'way' => $way])->first();
        if ($notice) {
            $status = $request->input('status');
            if ($status && $status > 0) {
                //只返回单次审核模板内容
                if ($status > $form->reviewTimes) {
                    return response()->json();
                }
                return response()->json($notice->data['review'][$status]);
            }
            return response()->json($notice);
        }
        return response()->json();
    }


    /**
     * 发布
     */
    public function publish($id)
    {
        return view('web/form/publish')->withForm(Form::findOrFail($id));
    }

    /**
     * 报表
     */
    public function report($id)
    {
        return view('web/form/report')->withForm(Form::findOrFail($id));
    }

    /**
     * 获得可选域的统计数据
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStat(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('datalist', $form);
        $data = $this->getData($request, $id);
        $filterArr = ['checkbox', 'select', 'radio-group', 'checkbox-group'];
        $items = [];
        foreach (json_decode($form->fields) as $item) {
            if (in_array($item->type, $filterArr)) {
                if ($item->type == 'checkbox') {
                    $item->values = [['label' => '是', 'value' => 'on'], ['label' => '否', 'value' => 'off']];
                }
                $items[$item->name] = $item;
            }
        }
        foreach ($data as $datum) {
            foreach ($items as $name => $item) {
                switch ($item->type) {
                    case 'checkbox':
                        if ($datum->$name == 'on') {
                            if (empty($item->stat['on'])) {
                                $item->stat['on'] = 0;
                            }
                            $item->stat['on'] += 1;
                        } else {
                            if (empty($item->stat['off'])) {
                                $item->stat['off'] = 0;
                            }
                            $item->stat['off'] += 1;
                        }
                        break;
                    case 'select':
                    case 'radio-group':
                        if (empty($item->stat[$datum->$name])) {
                            $item->stat[$datum->$name] = 0;
                        }
                        $item->stat[$datum->$name] += 1;
                        break;
                    case 'checkbox-group':
                        foreach ($datum->$name as $value) {
                            if (empty($item->stat[$value])) {
                                $item->stat[$value] = 0;
                            }
                            $item->stat[$value] += 1;
                        }
                        break;
                }
            }
        }
        return response()->json($items);
    }

    /**
     * 我的提交历史
     */
    public function history()
    {
        $data = FormData::where('user_id', Auth::user()->id)->get();
        $data = $data->filter(function ($item) {
            $form = Form::find($item->fid);
            if ($form) {
                $item->formname = $form->name;
                return true;
            }
            return false;
        });
        return view('web.form.history', ['data' => $data->all()]);
    }

    /**
     * 获得微信平台
     * @return array
     */
    private function getPlatforms()
    {
        $wechatPlatforms = WechatPlatform::where('owner_id', Auth::user()->id)->get();
        $platforms = [];
        foreach ($wechatPlatforms as $platform) {
            $platforms[] = ['id' => $platform->id, 'name' => $platform->nick_name];
        }
        return $platforms;
    }

    /**
     * 自定义样式
     */
    public function custom($id)
    {
        $form = Form::findOrFail($id);
        $fields = $form->fields;
        $fieldsStr = [];
        if ($fields) {
            $fieldsArr = json_decode($fields, true);
            foreach ($fieldsArr as $item) {
                if(isset($item['name'])) {
                    $fieldsStr[] = $item['label'] . '|' . $item['name'];
                }
            }
            $fieldsStr = implode(',', $fieldsStr);
        }
        return view('web.form.custom')->with(['form' => $form, 'fieldsStr' => $fieldsStr]);
    }

    /**
     * 上传静态文件or自定义html页面
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $type)
    {
        if ($type == 'html') {
            if ($request->hasFile($type)) {
                $fid = $request->input('id');
                $form = Form::find($fid);
                if ($form) {
                    //需要有更新权限
                    $this->authorize('update', $form);
                    $html = file_get_contents($request->file($type)->getRealPath());
                    $form->customView = $html;
                    $form->customStatus = Form::REVIEW_WAITING;
                    $form->save();
                    return response()->json(['status' => 0]);
                }
            }
        } else {
            if ($request->hasFile('assets')) {
                $responseUrl = [];
                foreach ($request->file('assets') as $item) {
                    $fileName = $item->getClientOriginalName();
                    preg_match('/\.\w+$/', $fileName, $matchs);
                    $file = 'custom_assets/' . uniqid() . $matchs[0];
                    Storage::put(
                        $file,
                        file_get_contents($item->getRealPath()),
                        'public'
                    );
                    $url = url(Storage::url($file));
                    $responseUrl[] = "<a target='_blank' href='{$url}'>{$url}</a>";
                }
                return response()->json([
                    'initialPreview' => $responseUrl,
                    'initialPreviewConfig' => [
                        [
                            'caption' => $fileName,
                            'type' => 'text',
                            'width' => '30px',
                            'frameAttr' => [
                                'style' => 'height: 100px;width: auto;display: flex',
                            ]
                        ]
                    ],
                    'initialPreviewShowDelete' => false,
                    'append' => false
                ]);
            }
        }
        return response()->json(['error' => '上传出错']);
    }

    /**
     * 自定义样式保存表单域
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFiled(Request $request, $id)
    {
        $form = Form::findOrFail($id);
        $this->authorize('update', $form);
        $fieldInput = $request->input('field');
        $fields = explode(',', $fieldInput);
        $fieldNames = [];
        $fieldData = [];
        foreach ($fields as $field) {
            $fieldArr = explode('|', $field);
            if (count($fieldArr) < 2) {
                return response()->json(['status' => 1, 'error' => '格式错误']);
            }
            $fieldNames[] = $fieldArr[1];
            $fieldData[] = [
                'label' => $fieldArr[0],
                'name' => $fieldArr[1],
                "type" => "text",
                "className" => "form-control",
            ];
        }
        $form->fieldNames = json_encode($fieldNames);
        $form->fields = json_encode($fieldData);
        $form->save();
        return response()->json(['status' => 0, 'error' => '']);
    }

    /**
     * 展示自定义样式申请列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customReviewList()
    {
        if (Auth::user()->super) {
            $forms = Form::whereNotNull('customView')->where('customStatus', Form::REVIEW_WAITING)->get();
            return view('web.form.customReviewList', ['data' => $forms]);
        }
    }

    /**
     * 自定义样式审核
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customReview(Request $request)
    {
        if (Auth::user()->super) {
            $id = $request->input('id');
            $result = $request->input('result');
            $reason = $request->input('reason');
            $form = Form::findOrFail($id);
            $form->customStatus = $result == 1 ? Form::REVIEW_PASS : Form::REVIEW_REFUSE;
            $form->customReview = $reason;
            $form->save();
            //通知创建者
            $email = $form->owner->email;
            $user = $form->owner->name;
            Mail::send('web.emails.customreview', ['result' => $result, 'reason' => $reason, 'form' => $form],
                function ($m) use ($email, $user) {
                    $m->from(env('MAIL_USERNAME'), env('MAIL_NAME'));
                    $m->to($email, $user)->subject('自定义样式审核通知');
                });
            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => 1, 'error' => '无权限操作']);
    }

    /**
     * 更改表单限制次数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function limit(Request $request)
    {
        $id = $request->input('id');
        $limitTimes = $request->input('limitTimes');
        $limitBy = $request->input('limitBy');
        if ($id && $limitTimes && $limitBy) {
            $form = Form::find($id);
            if (!$form) {
                return response()->json(['status' => 1, 'error' => '表单不存在']);
            }
            $this->authorize('update', $form);

            $form->limitTimes = $limitTimes;
            $form->limitBy = $limitBy;

            if ($form->save()) {
                return response()->json(['status' => 0, 'error' => '']);
            }
        }
        return response()->json(['status' => 1, 'error' => '操作有误，请重试。']);
    }
}
