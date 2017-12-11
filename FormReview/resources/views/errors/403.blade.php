@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="title" style="text-align: center;">
            <h1 style="color: orange">403</h1>
            <div style="padding: 5px; font-size: 25px;">对不起，您没有权限进行此操作。</div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        setTimeout(function () {
            window.history.back();
        }, 2000);
    </script>
@endsection
