@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="title" style="text-align: center;">
            <h1 style="color: orange">404</h1>
            <div style="padding: 5px; font-size: 25px;">对不起，您要找的页面不存在</div>
            <div class="redirect">即将返回<a href="/">主页</a></div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        setTimeout(function () {
            window.location = '/';
        }, 2000);
    </script>
@endsection
