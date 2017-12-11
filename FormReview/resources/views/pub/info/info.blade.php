@extends('layouts.app')

@section('content')
    <div class="container">
        @if(isset($form))
            <h4 class="form-title">{{$form->name}}</h4>
            @if(isset($status))
                <h6 class="form-title">审核进度：<span class="form-status"
                                                  data-reviewTimes="{{$form->reviewTimes}}">{{$status}}</span></h6>
            @endif
            @if($form->pic)
                <img src="{{$form->pic}}" alt="" class="form-pic img-responsive img-rounded">
            @endif
            <div class="form-description">{{$form->description}}</div>
            @if(!Auth::guest())
                <div class="form-import">
                    <button data-user="{{Auth::user()}}" data-info="{{Auth::user()->info}}"
                            class="btn btn-info btn-block btn-form-import" type="button">导入个人信息
                    </button>
                </div>
            @endif
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="fields" data-value="{{$fieldsData}}"></div>
        <div id="filldata" data-value="{{$data}}"></div>
        <form id="form-rander" method="POST" action="{{$url}}" accept-charset="UTF-8"
              class="form-horizontal form-fields" enctype="multipart/form-data">
            <textarea action="#" id="fb-template">
                <form-template><fields></fields></form-template>
            </textarea>
        </form>
        @if(isset($reviews))
            <div id="old-review">
                @foreach($reviews as $review)
                    <div class="panel panel-{{$review->result==1?'success' : 'danger'}}">
                        <div class="panel-heading"><span class="reviewStatus">{{$review->status}}</span>
                            <small style="color: #5d6d7e">{{$review->updated_at}}</small>
                        </div>
                        <div class="panel-body">
                            <p><span class="glyphicon glyphicon-pencil"></span>{{$review->evaluation}}</p>
                            <p><span class="glyphicon glyphicon-comment"></span>{{$review->remark}}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if(isset($remarks))
            <div id="old-remark">
                @foreach($remarks as $remark)
                    <div class="well">
                        <p>
                            <small style="color: #5d6d7e">{{$remark->updated_at}}</small>
                        </p>
                        <p>{{$remark->remark}}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/node_modules/bootstrap-checkbox/dist/js/bootstrap-checkbox.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/flat-ui/js/flatui-radio.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/flat-ui/js/flatui-checkbox.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/formBuilder/dist/form-render.js')}}"></script>
    <script src="{{cdnAsset('/assets/js/jquery.formautofill.min.js')}}"></script>
    <script>
        $('title').html('{{$form->name or '表单'}}');
        var btn_form_import = $('.btn-form-import');
        var $canEdit = 'true';
        @if(isset($canEdit))
            $canEdit = '{{$canEdit}}';
        @endif
    $(function () {
            var filldata = JSON.parse($('#filldata').attr('data-value'));
            var formData = $('#fields').attr('data-value');
            var formContainer = document.getElementById('form-rander');
            var formRenderOpts = {
                container: formContainer,
                dataType: 'json',
                formData: formData
            };
            $(formContainer).formRender(formRenderOpts);

            var reanderedForm = $('.rendered-form');
            if ($canEdit != 'true') {
                $('input').prop('disabled', true);
                btn_form_import.remove();
            } else {
                reanderedForm.append('{{ csrf_field() }}');
                reanderedForm.append('<div class="btn-form-submit"><button class="btn btn-primary btn-block" type="submit">提交</button></div>');
            }

            //表单状态
            var status = $('.form-status');
            if (status.length > 0) {
                var $reviewTimes = status.attr('data-reviewTimes');
                parseInt(status.text()) < 0 ? status.css('color', '#e74c3c') : status.css('color', '#0088cc');
                status.text(status2zh(status.text(), $reviewTimes));
            }

            //一键导入
            if (btn_form_import.length > 0) {
                var map = {
                    '地址': 'address',
                    '城市': 'city',
                    '班级': 'class',
                    '学校': 'college',
                    '国家': 'country',
                    '学制': 'eductional_system',
                    '邮箱': 'email',
                    '年级': 'grade',
                    '头像': 'headimgurl',
                    '专业': 'major',
                    '昵称': 'name',
                    '学号': 'number',
                    '电话': 'phone',
                    '省份': 'province',
                    '姓名': 'realname',
                    '学院': 'school',
                    '性别': 'sex',
                    '校区': 'zone'
                };
                var userinfo = JSON.parse(btn_form_import.attr('data-info'));
                $.extend(userinfo, JSON.parse(btn_form_import.attr('data-user')));
                btn_form_import.click(function () {
                    $.each($('#form-rander .fb-text'), function (i, item) {
                        $.each(map, function (cName, eName) {
                            if ($(item).find('label').text().indexOf(cName) > -1) {
                                $(item).find('input').val(userinfo[eName]);
                            }
                        });
                    });

                });
            }

            //审核转为中文
            var reviewStatus = $('.reviewStatus');
            if (reviewStatus.length > 0) {
                $.each(reviewStatus,function (i, item) {
                    $(item).text(reviewTime2zh($(item).text()));
                });
            }

            //显示之前的文件
            var $fileInputs = $('input[type="file"]');
            if ($fileInputs.length > 0) {
                $.each($fileInputs, function (i, item) {
                    var $itemName = $(item).attr('name');
                    var $fileUrl = filldata[$itemName];
                    if ($fileUrl) {
                        $(item).before('<a href="' + $fileUrl + '">上传的文件</a>');
                    }
                    delete filldata[$itemName];
                });
            }


            var autofillOpts = {
                restrict: false,
                findbyname: true
            };
            try {
                reanderedForm.autofill(filldata, autofillOpts);
            } catch (e) {
                console.log(e)
            }

            $(':radio').radiocheck();
            $(':checkbox').checkboxpicker();
//            $("select").select2({dropdownCssClass: 'dropdown-inverse'});
        });
    </script>
@endsection