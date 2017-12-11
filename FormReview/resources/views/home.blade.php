@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">基本信息</h3>
            </div>
            <ul class="list-group">
                <li class="list-group-item">昵称：<span data-model="{{\App\User::class}}" data-name="name"
                                                     class="click-update label label-default">{{Auth::user()->name}}</span>
                </li>
                <li class="list-group-item">电话：<span id="phone" class="label label-default" data-toggle="modal"
                                                     data-target=".update-phone-modal">{{Auth::user()->phone}}</span>
                </li>
                <div class="modal fade update-phone-modal" tabindex="-1" role="dialog" aria-labelledby="updatePhone">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">修改手机号</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <input id="newPhone" name="newPhone" type="text" class="form-control"
                                           placeholder="请输入新手机号"/>
                                </div>
                                <div class="form-group">
                                    <input id="code" name="code" type="text" class="form-control"
                                           placeholder="请输入收到的验证码"/>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-default send-code">发送验证码</button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                <button type="button" class="btn btn-primary btn-update-phone">确定</button>
                            </div>
                        </div>
                    </div>
                </div>
                <li class="list-group-item">邮箱：<span id="email" class="label label-default" data-toggle="modal"
                                                     data-target=".update-email-modal">{{Auth::user()->email}}</span></li>
                <div class="modal fade update-email-modal" tabindex="-1" role="dialog" aria-labelledby="updateEmail">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">修改邮箱</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <input id="newEmail" name="newEmail" type="text" class="form-control"
                                           placeholder="请输入新邮箱"/>
                                </div>
                                <div class="form-group">
                                    <input id="emailCode" name="code" type="text" class="form-control"
                                           placeholder="请输入收到的验证码"/>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-default send-email">发送验证码</button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                <button type="button" class="btn btn-primary btn-update-email">确定</button>
                            </div>
                        </div>
                    </div>
                </div>
            </ul>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">微信信息</h3>
            </div>
            @if(Auth::user()->unionid)
                <ul class="list-group">
                    <li class="list-group-item">昵称：<span class="label label-default">{{Auth::user()->nickname}}</span>
                    </li>
                    <li class="list-group-item">省份：<span class="label label-default">{{Auth::user()->province}}</span>
                    </li>
                    <li class="list-group-item">城市：<span class="label label-default">{{Auth::user()->city}}</span></li>
                    <li class="list-group-item">国家：<span class="label label-default">{{Auth::user()->country}}</span>
                    </li>
                    <li class="list-group-item">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bindWechat">
                            更新微信信息
                        </button>
                        <div class="modal fade bindWechat" tabindex="-1" role="dialog"
                             aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="text-align: center;padding-top: 10px;">
                                    <div id="wechatQrcode"></div>
                                    <div>请使用微信扫一扫</div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            @else
                <div class="panel-body" style="text-align: center;">
                    <div id="wechatQrcode"></div>
                    <div>请使用微信扫一扫</div>
                </div>
            @endif
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">学生信息</h3>
            </div>
            @if($userInfo)
                <ul class="list-group">
                    <li class="list-group-item">姓名：<span data-model="{{\App\UserInfo::class}}" data-name="realname"
                                                         class="click-update label label-default">{{$userInfo->realname}}</span>
                    </li>
                    <li class="list-group-item">学号：<span data-model="{{\App\UserInfo::class}}" data-name="number"
                                                         class="click-update label label-default">{{$userInfo->number}}</span>
                    </li>
                    <li class="list-group-item">班级：<span data-model="{{\App\UserInfo::class}}" data-name="class"
                                                         class="click-update label label-default">{{$userInfo->class}}</span>
                    </li>
                    <li class="list-group-item">年级：<span data-model="{{\App\UserInfo::class}}" data-name="grade"
                                                         class="click-update label label-default">{{$userInfo->grade}}</span>
                    </li>
                    <li class="list-group-item">专业：<span data-model="{{\App\UserInfo::class}}" data-name="major"
                                                         class="click-update label label-default">{{$userInfo->major}}</span>
                    </li>
                    <li class="list-group-item">学院：<span data-model="{{\App\UserInfo::class}}" data-name="school"
                                                         class="click-update label label-default">{{$userInfo->school}}</span>
                    </li>
                    <li class="list-group-item">学制：<span data-model="{{\App\UserInfo::class}}"
                                                         data-name="eductional_system"
                                                         class="click-update label label-default">{{$userInfo->eductional_system}}</span>
                    </li>
                    <li class="list-group-item">学校：<span data-model="{{\App\UserInfo::class}}" data-name="college"
                                                         class="click-update label label-default">{{$userInfo->college}}</span>
                    </li>
                    <li class="list-group-item">校区：<span data-model="{{\App\UserInfo::class}}" data-name="zone"
                                                         class="click-update label label-default">{{$userInfo->zone}}</span>
                    </li>
                    <li class="list-group-item">地址：<span data-model="{{\App\UserInfo::class}}" data-name="address"
                                                         class="click-update label label-default">{{$userInfo->address}}</span>
                    </li>
                </ul>
            @else
                <form class="form-horizontal" role="form" action="{{route('home.saveInfo')}}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">姓名</label>
                        <div class="col-sm-10">
                            <input name="name" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">学号</label>
                        <div class="col-sm-10">
                            <input name="number" type="number" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">班级</label>
                        <div class="col-sm-10">
                            <input name="class" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">年级</label>
                        <div class="col-sm-10">
                            <input name="grade" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">专业</label>
                        <div class="col-sm-10">
                            <input name="major" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">学院</label>
                        <div class="col-sm-10">
                            <input name="school" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">学制</label>
                        <div class="col-sm-10">
                            <select name="eductional_system"
                                    class="form-control select select-default select-block mbl">
                                <option value="2">两年制</option>
                                <option value="3">三年制</option>
                                <option value="4" selected="selected">四年制</option>
                                <option value="5">五年制</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">学校</label>
                        <div class="col-sm-10">
                            <input name="college" type="text" placeholder="" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">校区</label>
                        <div class="col-sm-10">
                            <input name="zone" type="text" placeholder="" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="form" class="col-sm-2 control-label">地址</label>
                        <div class="col-sm-10">
                            <input name="address" type="text" placeholder="" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">保存</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/assets/js/jquery.qrcode.min.js')}}"></script>
    <script>
        var _token = '<?php echo csrf_token(); ?>';
        $("select").select2({dropdownCssClass: 'dropdown-inverse'});
        $('#wechatQrcode').qrcode('{{$bindUrl}}');
        /**
         * 修改信息
         * @param $input
         */
        function update($input) {
            var $value = $($input).val();
            //如果值没有改变，那么不提交
            if ($value == $($input).attr('data-origin')) {
                $($input).prev('span').show();
                $input.remove();
                return;
            }
            $.post('/home/update', {
                _token: _token,
                model: $($input).attr('data-model'),
                name: $($input).attr('name'),
                value: $value
            }, function (data) {
                if (data.status != 0) {
                    toastr.error(data.error);
                }
            });
            var $span = $($input).prev('span');
            $span.text($value);
            $span.show();
            $input.remove();
        }
        $(function () {
            /*点击修改*/
            $('.click-update').click(function () {
                var $name = $(this).attr('data-name');
                var $table = $(this).attr('data-model');
                var $value = $(this).text();
                $(this).after('<input data-origin="' + $value + '" data-model="' + $table + '" name="' + $name + '" type="text" value="' + $value + '" class="enter-submit" onblur="update(this)"/>');
                $(this).hide();
                $(this).next('input').focus();
            });

            /*发送验证码*/
            $('.send-code').click(function () {
                var $newPhone = $('#newPhone').val();
                if (!isPhone($newPhone)) {
                    toastr.error('手机号格式不正确');
                    return;
                }
                var leftTime=59;
                var interval = setInterval(function () {
                    var sendBtn = $('.send-code');
                    if(leftTime<1) {
                        sendBtn.removeAttr("disabled");
                        sendBtn.text("发送验证码");
                        clearInterval(interval);
                    }else {
                        sendBtn.attr("disabled", 'disabled');
                        sendBtn.text(''+leftTime);
                        leftTime--;
                    }
                },1000);
                $.get('/sendCode/phone', {phone: $newPhone}, function (data) {
                    if (data.status == 0) {
                        toastr.success('验证码已发送到您的手机号上，请查收');
                    } else {
                        toastr.error('验证码发送失败，请稍后重试');
                    }
                });
            });
            /*更新手机号*/
            $('.btn-update-phone').click(function () {
                var $newPhone = $('#newPhone').val();
                $.post('/home/update', {
                    _token: _token,
                    model: 'App\\User',
                    name: 'phone',
                    value: $newPhone,
                    code: $('#code').val()
                }, function (data) {
                    if (data.status != 0) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('更改成功！');
                        $('.update-phone-modal').modal('hide');
                        $('#phone').text($newPhone);
                    }
                });
            });

            /*发送邮箱验证码*/
            $('.send-email').click(function () {
                var $newEmail = $('#newEmail').val();
                if (!$newEmail.match(/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/)) {
                    toastr.error('邮箱格式不正确');
                    return;
                }
                var leftTime=59;
                var interval = setInterval(function () {
                    var sendBtn = $('.send-email');
                    if(leftTime<1) {
                        sendBtn.removeAttr("disabled");
                        sendBtn.text("发送验证码");
                        clearInterval(interval);
                    }else {
                        sendBtn.attr("disabled", 'disabled');
                        sendBtn.text(''+leftTime);
                        leftTime--;
                    }
                },1000);
                $.get('/sendCode/email', {email: $newEmail}, function (data) {
                    if (data.status == 0) {
                        toastr.success('验证码已发送到您的邮箱上，请查收');
                    } else {
                        toastr.error('验证码发送失败，请稍后重试');
                    }
                });
            });
            /*更新邮箱*/
            $('.btn-update-email').click(function () {
                var $newEmail = $('#newEmail').val();
                $.post('/home/update', {
                    _token: _token,
                    model: 'App\\User',
                    name: 'email',
                    value: $newEmail,
                    code: $('#emailCode').val()
                }, function (data) {
                    if (data.status != 0) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('更改成功！');
                        $('.update-email-modal').modal('hide');
                        $('#email').text($newEmail);
                    }
                });
            });

        });
    </script>
@endsection
