@extends('layouts.app')
@section('css')
    <link href="{{cdnAsset('/assets/css/welcome.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="container-fluid">
        <div class="part-one">
            <h2 class="title">在线审核 即刻可见</h2>
            <div class="subtitle"><span class="pc-subtitle">表单管理大师 —— </span>简单高效的在线表单管理平台</div>
            @if (Auth::guest())
            <a href="{{ url('/register') }}" class="btn btn-embossed btn-wide btn-primary btn-free-use">免费使用</a>
            @else
                <a href="{{ url('/web/form/create') }}" class="btn btn-embossed btn-wide btn-primary btn-free-use">免费使用</a>
            @endif
            <div class="feature row">
                <div class="col-xs-6 col-md-3"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> 快速创建</div>
                <div class="col-xs-6 col-md-3"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> 多人协作</div>
                <div class="col-xs-6 col-md-3"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> 多人审核</div>
                <div class="col-xs-6 col-md-3"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> 微信通知</div>
            </div>
        </div>
        <section class="part-two">
            <div class="step-wrapper">
                <div class="item">
                    <img class="logo" src="/storage/public/welcome/1.png">
                    <div class="desc">思考项目流程</div>
                </div>
                <div class="next-step">></div>
                <div class="item">
                    <img class="logo" src="/storage/public/welcome/2.png">
                    <div class="desc">创建项目表单</div>
                </div>
                <div class="next-step">></div>
                <div class="item">
                    <img class="logo" src="/storage/public/welcome/3.png">
                    <div class="desc">绑定微信平台</div>
                </div>
                <div class="next-step">></div>
                <div class="item">
                    <img class="logo" src="/storage/public/welcome/4.png">
                    <div class="desc">邀请管理员</div>
                </div>
                <div class="next-step">></div>
                <div class="item">
                    <img class="logo" src="/storage/public/welcome/5.png">
                    <div class="desc">项目发布</div>
                </div>
            </div>
        </section>
    </div>
    <footer class="footer">
        <div class="bottom">©2017 表单管理大师 | <a target="_blank" href="http://linyii.com/">京ICP备16054701号-1</a></div>
    </footer>
@endsection
