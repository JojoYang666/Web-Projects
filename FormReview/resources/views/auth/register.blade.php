@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">注册</div>
                    <div class="panel-body" style="background-color: #edeff1;">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">昵称</label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                           required>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">手机号</label>

                                <div class="col-md-6">
                                    <input id="phone" type="tel" class="form-control" name="phone"
                                           value="{{ old('phone') }}"
                                           required>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">手机验证码</label>

                                <div class="col-md-4">
                                    <input type="number" class="form-control" name="code" required>

                                    @if ($errors->has('code'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-default col-md-2 send-code" onclick="sendCode()">发送验证码</button>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">邮箱</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"
                                           required>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('ecode') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">邮箱验证码</label>

                                <div class="col-md-4">
                                    <input type="number" class="form-control" name="ecode" required>

                                    @if ($errors->has('ecode'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('ecode') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-default col-md-2 send-ecode" onclick="sendECode()">发送验证码</button>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">密码</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">确认密码</label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" name="password_confirmation" required>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        <i class="fa fa-btn fa-user"></i>注册
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <style>
        body {
            background: #1abc9c;
        }
    </style>
@endsection
@section('js')
    <script>
        function sendCode() {
            var $phone = $('#phone').val();
            if($phone) {
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
                $.get('/sendCode/phone',{phone:$phone},function (data) {
                    if(data.status==0) {
                        toastr.success('验证码已发送到您的手机号上，请查收');
                    }else {
                        toastr.error('验证码发送失败，请稍后重试');
                    }
                });
            }
        }
        function sendECode() {
            var $email = $('#email').val();
            if($email) {
                $.get('/sendCode/email',{email:$email},function (data) {
                    if(data.status==0) {
                        toastr.success('验证码已发送到您的邮箱上，请查收');
                        var leftTime=59;
                        var interval = setInterval(function () {
                            var sendBtn = $('.send-ecode');
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
                    }else {
                        toastr.error('验证码发送失败，请稍后重试');
                    }
                });
            }
        }
    </script>
@endsection
