<?php

namespace App\Http\Controllers\Web;

use App\Form;
use App\FormAdmin;
use App\Http\Controllers\Controller;
use App\RedisKey;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Validator;
use Illuminate\Support\Facades\Auth;

class FormAdminController extends Controller
{
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
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    /**
     * 管理员设置
     * @param $fid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manageAdmins($fid)
    {
        $form = Form::findOrFail($fid);
        $this->authorize('creator', $form);
        $columns = [];
        $columns[] = ['field' => 'state', 'checkbox' => 'true', 'align' => 'center'];
        $columns[] = [
            'field' => 'username',
            'title' => '管理员',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'stage',
            'title' => '审核批次',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'authorities',
            'title' => '权限',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input",
            'editable' => true,
        ];
        $columns[] = [
            'field' => 'conditions',
            'title' => '过滤条件',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input",
            'editable' => true,
        ];
        $columns[] = [
            'field' => 'handle',
            'title' => '状态',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "select"
        ];
        $columns[] = [
            'field' => 'updated_at',
            'title' => '更新时间',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input"
        ];
        $columns[] = [
            'field' => 'remark',
            'title' => '备注',
            'align' => 'center',
            'sortable' => true,
            'filterControl' => "input",
            'editable' => true,
        ];
        $columns[] = [
            'field' => 'operate',
            'title' => '操作',
            'align' => 'center',
            'events' => 'operateEvents',
            'formatter' => 'operateFormatter'
        ];
        return view('web.form.admins', ['form' => $form, 'columns' => json_encode($columns)]);
    }

    public function getAdmins($fid)
    {
        $form = Form::findOrFail($fid);
        $this->authorize('creator', $form);
        $data = $form->admins;
//        $data = $data->filter(function ($item) {
//            return $item->user_id != Auth::user()->id;
//        });
        foreach ($data as $item) {
            $item->username = $item->user->name;
        }
        return response()->json($data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $formId = $request->input('formId');
        $forms = Form::where('creator', Auth::user()->id)->get();
        return view('web.form.invite_vue', ['forms' => json_encode($forms), 'formId' => $formId]);
    }

    //获得自己创建的表单
    public function getOwnForms()
    {
        return response()->json(Auth::user()->ownForms);
    }

    //获得自己创建的表单
    public function getAuthorities()
    {
        return response()->json(Form::$authorities);
    }

    /**
     * 邀请管理员
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'tableId' => 'required',
            'authorities' => 'required',
            'stages' => 'required',
            'users' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json(['status'=>1,'error'=>$v->errors()],401);
        }
        $formId = intval($request->input('tableId'));
        $form = Form::findOrFail($formId);
        $this->authorize('creator', $form);

        $userIds = $request->input('users');
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (empty($user)) {
                return response()->json(['status'=>2,'error'=>'用户不存在'],404);
            }
            $admin = FormAdmin::where('user_id', $userId)->where('form_id', $formId)->first();
            if (!$admin) {
                $admin = new FormAdmin();
            }
            $admin->form_id = $formId;
            $admin->inviter_id = Auth::user()->id;
            $admin->authorities = json_encode($request->input('authorities'));
            $admin->stage = json_encode($request->input('stages'));
            $condition = $request->input('add_condition');
            if ($condition) {
                $conditions = $request->input('conditions');
            }else{
                $conditions = [];
            }
            $admin->conditions = json_encode($conditions);
            $admin->user_id = $userId;
            $admin->remark = $request->input('remark');
            $admin->handle = false;
            $admin->save();
            //发送邮件
            Mail::send('web.emails.invite_msg', ['form' => $form], function ($m) use ($user) {
                $m->from(env('MAIL_USERNAME'), env('APP_NAME'));
                $m->to($user->email, $user->name)->subject('管理员邀请');
            });
        }
        return response()->json(['status'=>0,'error'=>'成功发送邀请']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * 暂无次功能
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $formAdmin = FormAdmin::findOrFail($id);
        $this->authorize('update', $formAdmin);

    }

    /**
     * 暂无次功能
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formAdmin = FormAdmin::findOrFail($id);
        $this->authorize($formAdmin);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $formAdmin = FormAdmin::findOrFail($id);
        $this->authorize($formAdmin);
        $formAdmin->delete();
        return response()->json(['status' => 0]);
    }

    /**
     * 获取没有管理此表单的用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExceptUsers(Request $request)
    {
        $fid = $request->input('fid');
        $search = $request->input('q') or '';
        $fid = false;//为了提高性能，暂时不筛选
        if ($fid) {
            $users = User::where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->whereHas('forms', function ($q) use ($fid) {
                $q->where('forms.id', '=', $fid);

            })->get();
            $users = User::all()->diff($users)->toArray();
        } else {
            $users = User::select('id', 'name', 'phone', 'email')
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->get()->diff([Auth::user()])->toArray();
        }
        return response()->json($users);
    }

    public function message()
    {
        $messages = FormAdmin::where('user_id', Auth::user()->id)->notselfinvite()->notdeluser()->with('inviter',
            'form')->get();
        $sendMessages = FormAdmin::where('inviter_id', Auth::user()->id)->notinviteself()->notdelinviter()->with('user',
            'form')->get();
        return view('web.form.message', ['messages' => $messages, 'sendMsg' => $sendMessages]);
    }

    /**
     * get 消息数量
     */
    public function inviteMsgNum()
    {
        $inviteMsgNum = Cache::remember(RedisKey::inviteMsgNumKey(), self::CACHE_MIN, function() {
            return FormAdmin::where('user_id',
                Auth::user()->id)->notselfinvite()->notdeluser()->notHandle()->count();
        });
        return response()->json(['status' => 0, 'inviteMsgNum' => $inviteMsgNum]);
    }

    /**
     * 删除邀请消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMsg(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $msg = FormAdmin::find($id);
        if (!$msg) {
            return response()->json(['status' => 1, 'error' => '没有此消息']);
        }
        if ($type == 'user' && $msg->user_id == Auth::user()->id) {
            $msg->user_del_msg = true;
            $msg->save();
            return response()->json(['status' => 0]);
        }
        if ($type == 'inviter' && $msg->inviter_id == Auth::user()->id) {
            $msg->inviter_del_msg = true;
            $msg->save();
            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => 2, 'error' => '您无权进行此操作！']);
    }

    /**
     * 异步处理消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        $id = intval($request->input('id'));
        $handle = $request->input('handle');
        $formAdmin = FormAdmin::findOrFail($id);
        $this->authorize($formAdmin);
        $formAdmin->handle = intval($handle);
        if ($formAdmin->save()) {
            return response()->json(['status' => 0]);
        }
        return response()->json(['status' => 1]);
    }
}
