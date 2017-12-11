@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            @foreach($messages as $message)
                @if(isset($message->form))
                    <div data-handle="{{$message->handle}}" class="panel panel-{{$message->handle?'default':'info'}}"
                         id="msg{{$message->id}}">
                        <div class="panel-heading" role="tab" id="heading{{$message->id}}">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$message->id}}"
                                   aria-expanded="true" aria-controls="collapseOne">
                                    【管理员邀请】
                                    <small style="color: #5d6d7e">{{$message->created_at}}</small>
                                </a>
                                <span class="glyphicon glyphicon-trash msg-delete" aria-hidden="true"
                                      onclick="deleteMsg('{{$message->id}}','user')"></span>
                                @if($message->handle)
                                    <span class="label label-default">{{$message->handle==1?'已接受':'已拒绝'}}</span>
                                @endif
                            </h4>
                        </div>
                        <div id="collapse{{$message->id}}" class="panel-collapse collapse in" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <p>{{$message->inviter->name}}邀请您成为<a
                                            href="{{route('web.form.show',$message->form->id)}}">{{$message->form->name}}</a>的管理员，您将拥有
                                    @foreach(json_decode($message->authorities) as $authority)
                                        【{{\App\Form::$authorities[$authority]}}】
                                    @endforeach
                                    权限。</p>
                                @if(!empty($message->conditions))
                                    <p>您的过滤条件为：</p>
                                    <p>
                                        @foreach(json_decode($message->conditions) as $condition)
                                            {{$condition->label}}在{{$condition->value}}中
                                        @endforeach
                                    </p>
                                @endif
                                @if(!$message->handle)
                                    <button class="btn btn-embossed btn-success accept"
                                            onclick="handleMsg('{{$message->id}}','{{\App\FormAdmin::ACCEPT}}')">接收
                                    </button>
                                    <button class="btn btn-embossed btn-warning refuse"
                                            onclick="handleMsg('{{$message->id}}','{{\App\FormAdmin::REFUSE}}')">拒绝
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @foreach($sendMsg as $message)
                @if(isset($message->form))
                    <div data-handle="{{$message->handle}}" class="panel panel-{{$message->handle?'default':'success'}}"
                         id="msg{{$message->id}}">
                        <div class="panel-heading" role="tab" id="heading{{$message->id}}">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$message->id}}"
                                   aria-expanded="true" aria-controls="collapseOne">
                                    【邀请管理员】
                                    <small style="color: #5d6d7e">{{$message->created_at}}</small>
                                </a>
                                <span class="glyphicon glyphicon-trash msg-delete" aria-hidden="true"
                                      onclick="deleteMsg('{{$message->id}}','inviter')"></span>
                            </h4>
                        </div>
                        <div id="collapse{{$message->id}}" class="panel-collapse collapse in" role="tabpanel"
                             aria-labelledby="headingOne">
                            <div class="panel-body">
                                <p>您邀请{{$message->user->name}}成为<a
                                            href="{{route('web.form.show',$message->form->id)}}">{{$message->form->name}}</a>的管理员，他将拥有
                                    @foreach(json_decode($message->authorities) as $authority)
                                        【{{\App\Form::$authorities[$authority]}}】
                                    @endforeach
                                    权限。</p>
                                @if(!empty($message->conditions))
                                    <p>他的过滤条件为：</p>
                                    <p>
                                        @foreach(json_decode($message->conditions) as $condition)
                                            {{$condition->label}}在{{$condition->value}}中
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
@section('js')
    <script>
        function handleMsg($id, $res) {
            $.post('/web/admin/handle', {
                id: $id,
                handle: $res
            }, function (data) {
                if (data.status == 0) {
                    toastr.success('处理成功！');
                    $('#msg' + $id).remove();
                } else {
                    toastr.error('处理失败，请刷新重试！');
                }
            })
        }
        function deleteMsg($id, $type) {
            $.post('/web/admin/deleteMsg', {
                id: $id,
                type: $type
            }, function (data) {
                if (data.status == 0) {
                    toastr.success('删除成功！');
                    $('#msg' + $id).remove();
                } else {
                    toastr.error('删除失败，' + data.error);
                }
            })
        }
    </script>
@endsection