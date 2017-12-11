@extends('layouts.app')
@section('css')
    <style>
        body {
            background: #1abc9c;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row" style="margin-top: 10%;">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body" style="background-color: #edeff1;">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}"
                                           placeholder="请输入邮箱或手机号">
                                    <span class="form-control-feedback fui-user"></span>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="col-md-12">
                                    <input type="password" class="form-control" name="password" placeholder="请输入密码">
                                    <span class="form-control-feedback fui-lock"></span>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group" style="display: none;">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" checked> 记住我
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        <i class="fa fa-btn fa-sign-in"></i>登录
                                    </button>

                                    <a class="login-link" href="{{ url('/password/reset') }}">忘记密码？</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
