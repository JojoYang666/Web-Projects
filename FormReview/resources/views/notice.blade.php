@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="title" style="text-align: center;">
            <h1>{{$notice}}</h1>
            <div style="padding: 5px; font-size: 16px;"></div>
            @if(isset($qrcode))
                <div id="qrcode"></div>
            @endif
            @if(isset($url))
                <div class="redirect">即将跳转<a href="{{$url}}">{{$page or ''}}页面</a></div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/assets/js/jquery.qrcode.min.js')}}"></script>
    <script>
        @if(isset($qrcode)&&is_string($qrcode))
            $('#qrcode').append('<img src="{{$qrcode}}" style="width: 200px">');
        @elseif(isset($qrcode))
            $('#qrcode').qrcode(window.location.href);
        @endif
        @if(isset($url))
            setTimeout(function () {
            window.location = '{{$url}}';
        }, 2000);
        @endif
    </script>
@endsection
