@extends('layouts.app')
@section('css')
    <style>
        @font-face {
            font-family: 'iconfontyyy';
            src: url('{{cdnAsset('/assets/fonts/share/iconfont.eot')}}');
            src: url('{{cdnAsset('/assets/fonts/share/iconfont.eot')}}?#iefix') format('embedded-opentype'),
            url('{{cdnAsset('/assets/fonts/share/iconfont.woff')}}') format('woff'),
            url('{{cdnAsset('/assets/fonts/share/iconfont.ttf')}}') format('truetype'),
            url('{{cdnAsset('/assets/fonts/share/iconfont.svg')}}#iconfontyyy') format('svg');
        }
        .iconfontyyy{
            font-family:"iconfontyyy" !important;
            font-size:16px;font-style:normal;
            -webkit-font-smoothing: antialiased;
            -webkit-text-stroke-width: 0.2px;
            -moz-osx-font-smoothing: grayscale;}
    </style>
@endsection

@section('content')
    <div class="container">
        @include('web.form.nav')
        <div class="panel panel-default publish-url">
            <div class="panel-heading">
                <h3 class="panel-title">直接访问网址</h3>
            </div>
            <div class="panel-body">
                <form class="form-inline" role="form">
                    <div class="input-group">
                        <input class="form-control" id="form-url" type="text"
                               value="{{route('pub.info.create',$form->id)}}"/>
                        <a href="#" tabindex="0" class="input-group-addon" id="qrcode-btn" data-toggle="popover"><span
                                    class="glyphicon glyphicon-qrcode"></span> 二维码</a>
                    </div>
                    <button id="clipboard" class="btn btn-default" data-clipboard-target="#form-url"
                            data-toggle="tooltip" data-trigger="click" data-placement="bottom" title="复制成功！"><span
                                class="glyphicon glyphicon-bookmark"></span> 复制网址
                    </button>
                    <a target="_blank" href="{{route('pub.info.create',$form->id)}}" class="btn btn-default"><span
                                class="glyphicon glyphicon-new-window"></span> 直接打开</a>
                </form>
            </div>
        </div>
        快速分享至：<div data-url="{{route('pub.info.create',$form->id)}}" data-title="{{$form->title}}" data-description="{{$form->description}}" data-image="{{$form->pic}}" class="social-share" data-initialized="true">
            <a href="#" class="icon-weibo">微博</a>
            <a href="#" class="icon-qq">QQ</a>
            <a href="#" class="icon-wechat">微信</a>
            <a href="#" class="icon-qzone">QQ空间</a>
        </div>

    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/assets/js/jquery.qrcode.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/clipboard/dist/clipboard.min.js')}}"></script>
    <link rel="stylesheet" href="{{cdnAsset('/node_modules/social-share.js/dist/css/share.min.css')}}">
    <script src="{{cdnAsset('/node_modules/social-share.js/dist/js/social-share.min.js')}}"></script>
    <script>
        $('#nav-publish').addClass('active');

        //剪切板
        new Clipboard('#clipboard');
        var $copyBtn = $('#clipboard');
        $copyBtn.tooltip();
        $copyBtn.click(function () {
            window.setTimeout(function () {
                $copyBtn.tooltip('hide')
            }, 1000);
        });

        //二维码弹出框
        var popoverOption = {
            placement: "bottom",
            trigger: "focus",
            html: true,
            template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
            content: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
        };
        var $qrcodeBtn = $('#qrcode-btn');
        $qrcodeBtn.popover(popoverOption);
        $qrcodeBtn.on('shown.bs.popover', function () {
            $('.popover-content').append('<div class="qrcode"></div>');
            $('.qrcode').qrcode("{{route('pub.info.create',$form->id)}}");
        })
    </script>
@endsection