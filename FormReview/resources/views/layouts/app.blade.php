<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content=表单审核,在线审核,多级审核,项目管理,审核系统,表单设计,问卷调查,活动报名,考试测评,微信表单>
    <meta name="description" content=表单管理大师是一个简单好用的在线表单审核工具，帮你轻松完成日常数据的收集、整理和审核工作。应用场景覆盖全行业，包括问卷调查、意见反馈、活动报名、信息登记、在线订单、测评考试、在线抽奖等等，特别注重学校方面，学生可以一键导入自己的信息。表单管理大师紧密联系微信平台，可以通过微信直接登录系统，可以设置微信模板通知。管理员可以设置多级审核制度，可以根据表单的部分条件邀请管理员，简单、好用、高效。>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <title>表单管理大师</title>

    {{--<link href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>--}}
    {{--<link href="//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">--}}
    <link href="{{cdnAsset('/bower_components/flat-ui/dist/css/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/bower_components/flat-ui/dist/css/flat-ui.min.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/bootstrap-table/dist/bootstrap-table.min.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/toastr/build/toastr.min.css')}}" rel="stylesheet">

    <link href="{{cdnAsset('/node_modules/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/formBuilder/dist/form-builder.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/node_modules/formBuilder/dist/form-render.css')}}" rel="stylesheet">
    <link href="{{cdnAsset('/assets/css/app.css')}}" rel="stylesheet">
    {{--<link href="//cdn.bootcss.com/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">--}}

    @yield('css')
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?369891049b5980c3a8fdb4a46329a5ab";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body id="app-layout">
<nav class="header navbar navbar-inverse navbar-embossed" role="navigation">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand logo" href="{{ url('/') }}">
                <img src="/storage/public/logo.png" alt="">
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
                    <li><a href="{{ url('/login') }}"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 登录</a></li>
                    <li><a href="{{ url('/register') }}"><span class="glyphicon glyphicon-flag" aria-hidden="true"></span> 注册</a></li>
                @else
                    <li><a href="{{ route('web.message') }}"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span> 消息<span id="msgNum" class="badge"></span></a></li>

                    <li><a href="{{ url('/web/form') }}"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> 我的表单</a></li>
                    <li><a href="{{ url('/web/form/create') }}"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 创建表单</a></li>
                    <li><a href="{{ url('/web/form/history') }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 提交记录</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            @if(Auth::user()->headimgurl)
                                <img src="{{Auth::user()->headimgurl}}" alt="{{ Auth::user()->name }}"
                                     class="img-circle avatar">
                            @else
                                <div id="avatar" class="avatar" data-name="{{ Auth::user()->name }}"></div>
                            @endif
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/home"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>  {{ Auth::user()->name }}</a></li>
                            <li><a href="{{ route('web.wechat.index') }}"><span class="glyphicon glyphicon-cloud" aria-hidden="true"></span> 微信平台</a></li>
                            @if(Auth::user()->super)
                                <li class="divider"></li>
                                <li><a href="{{ url('/web/form/customReviewList') }}"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 自定义样式申请列表</a></li>
                            @endif
                            <li class="divider"></li>
                            <li><a href="{{ url('/logout') }}"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> 注销</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
            <form class="navbar-form navbar-right" action="{{route('web.form.index')}}" role="search">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" id="key" name="key" type="search" placeholder="Search">
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
<script src="{{cdnAsset('/bower_components/flat-ui/dist/js/vendor/html5shiv.js')}}"></script>
<script src="{{cdnAsset('/bower_components/flat-ui/dist/js/vendor/jquery.min.js')}}"></script>
<script src="{{cdnAsset('/bower_components/flat-ui/dist/js/vendor/respond.min.js')}}"></script>
{{--<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js')}}"></script>--}}
{{--<script src="{{cdnAsset('/bower_components/flat-ui/dist/js/vendor/video.js')}}"></script>--}}
<script src="{{cdnAsset('/bower_components/flat-ui/dist/js/flat-ui.min.js')}}"></script>
<script src="{{cdnAsset('/node_modules/typeahead.js/dist/typeahead.bundle.min.js')}}"></script>
<script src="{{cdnAsset('/node_modules/bootstrap-table/dist/bootstrap-table.min.js')}}"></script>
<script src="{{cdnAsset('/node_modules/bootstrap-table/dist/locale/bootstrap-table-zh-CN.min.js')}}"></script>
<script src="{{cdnAsset('/node_modules/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js')}}"></script>
<script src="{{cdnAsset('/assets/js/tableExport.js')}}"></script>
<script src="{{cdnAsset('/node_modules/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.min.js')}}"></script>
<script src="{{cdnAsset('/node_modules/bootstrap-table/dist/extensions/filter/bootstrap-table-filter.min.js')}}"></script>
{{--<script src="//cdn.bootcss.com/bootstrap-table/1.8.0/extensions/toolbar/bootstrap-table-toolbar.min.js')}}"></script>--}}
<script src="{{cdnAsset('/node_modules/toastr/build/toastr.min.js')}}"></script>
{{--<script src="//cdn.bootcss.com/TableExport/4.0.0-alpha.1/js/tableexport.min.js')}}"></script>--}}

{{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
<script src="{{cdnAsset('/assets/js/chan.avatar.js')}}"></script>
<script src="{{cdnAsset('/assets/js/app.js')}}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    @if (!Auth::guest())
    $.get('/web/admin/inviteMsgNum', function (data) {
        if (data.status == 0 && data.inviteMsgNum != 0) {
            $('#msgNum').html(data.inviteMsgNum);
        }
    });
    //头像
    var params = {
        width: "100%",
        height: "auto",
        fontSize: "28px"
    };
    $('#avatar').avatarIcon(params);
    @endif

</script>
@yield('js')
</body>
</html>
