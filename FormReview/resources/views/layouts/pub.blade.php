<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>

    {{--<link href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>--}}
    {{--<link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">--}}
    <link href="//cdn.bootcss.com/flat-ui/2.3.0/css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/flat-ui/2.3.0/css/flat-ui.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/bootstrap-table/1.11.0/bootstrap-table.min.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <link href="{{cdnAsset('/node_modules/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/formBuilder/dist/form-builder.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/formBuilder/dist/form-render.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/assets/css/app.css')}}" rel="stylesheet">
    {{--<link href="//cdn.bootcss.com/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">--}}

    @yield('css')
</head>
<body id="app-layout">
<nav class="header navbar navbar-inverse navbar-embossed" role="navigation">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                Form Review
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                {{--<li><a href="{{ url('/home') }}">表单</a></li>--}}
            </ul>

            <!-- Right Side Of Navbar -->

            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">登陆</a></li>
                    <li><a href="{{ url('/register') }}">注册</a></li>
                @else
                    <li><a href="{{ route('web.message') }}">消息<span id="msgNum" class="badge"></span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            表单 <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/web/form') }}"><i class="fa fa-btn fa-sign-out"></i>我的表单</a></li>
                            <li><a href="{{ url('/web/form/create') }}"><i class="fa fa-btn fa-sign-out"></i>创建表单</a></li>
                            <li><a href="{{ route('web.admin.create') }}"><i class="fa fa-btn fa-sign-out"></i>邀请管理员</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('web.wechat.index') }}">微信平台</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <img src="https://dn-jsjpub.qbox.me/av/5811a11a5e31a65d41e4c9c21477550362.png" alt="..." class="img-circle avatar"> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">{{ Auth::user()->name }}</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>注销</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
            <form class="navbar-form navbar-right" action="#" role="search">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" id="navbarInput-01" type="search" placeholder="Search">
                        <span class="input-group-btn">
            <button type="submit" class="btn"><span class="fui-search"></span></button>
          </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>

@yield('content')

<!-- JavaScripts -->
<script src="//cdn.bootcss.com/flat-ui/2.3.0/js/vendor/html5shiv.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.3.0/js/vendor/jquery.min.js"></script>
<script src="//cdn.bootcss.com/flat-ui/2.3.0/js/vendor/respond.min.js"></script>
{{--<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="//cdn.bootcss.com/flat-ui/2.3.0/js/vendor/video.js"></script>--}}
<script src="//cdn.bootcss.com/flat-ui/2.3.0/js/flat-ui.min.js"></script>
<script src="//cdn.bootcss.com/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.0/bootstrap-table.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.0/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.0/extensions/export/bootstrap-table-export.min.js"></script>
<script src="//rawgit.com/hhurz/tableExport.jquery.plugin/master/tableExport.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.0/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.0/extensions/filter/bootstrap-table-filter.min.js"></script>
{{--<script src="//cdn.bootcss.com/bootstrap-table/1.8.0/extensions/toolbar/bootstrap-table-toolbar.min.js"></script>--}}
<script src="//cdn.bootcss.com/toastr.js/latest/toastr.min.js"></script>
{{--<script src="//cdn.bootcss.com/TableExport/4.0.0-alpha.1/js/tableexport.min.js"></script>--}}

{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //        TODO 401
    $.get('/web/admin/inviteMsgNum',function (data) {
        if(data.status==0&&data.inviteMsgNum!=0) {
            $('#msgNum').html(data.inviteMsgNum);
        }
    });
</script>
@yield('js')
</body>
</html>
