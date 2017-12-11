@extends('layouts.app')

@section('content')
    <div class="container">
        @if($method=='PUT')
            @include('web.form.nav')
        @endif
        <div class="row" style="margin-bottom: 20px;">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>哎呀！出了些问题！</strong>

                    <br><br>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{$url}}" accept-charset="UTF-8" class="form-horizontal" role="form"
                  enctype="multipart/form-data">
                <input name="_method" type="hidden" value="{{$method}}">
                {{ csrf_field() }}
                <div class="form-group col-xs-12 col-md-12">
                    <label for="title" class="control-label required">表单标题</label>
                    <input class="form-control" required="required" name="title" type="text"
                           value="{{$form->title or ''}}" id="title">
                </div>
                <div class="form-group col-xs-12 col-md-12">
                    <label for="form_description" class="control-label">表单描述</label>
                    <textarea class="form-control" name="form_description" cols="50" rows="6"
                              id="form_description">{{$form->description or ''}}</textarea>
                </div>
                <div class="form-group col-xs-12 col-md-12">
                    <label for="pic" class="control-label">表单图片</label>
                    <input type="file" name="pic" id="pic" class="file">
                </div>
                <div class="form-group col-xs-12 col-md-12">
                    <label for="wechat" class="control-label">微信平台 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="如果选择了微信平台，那么只能通过微信端收集表单"></span></label>
                    <select id="wechat" name="wechat"
                            class="form-control select select-primary select-block mbl">
                        <option value="">不绑定微信平台</option>
                        @foreach($wechatPlatforms as $platform)
                            <option value="{{$platform['id']}}" {{isset($form)?($form->wechat==$platform['id']?'selected':''):''}}>{{$platform['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-xs-12 col-md-3 reviewTimes">
                    <div class="input-group">
                        <span for="reviewTimes" class="input-group-addon required">审核次数</span>
                        <input class="form-control" required="required" max="10" name="reviewTimes" type="number"
                               value="{{$form->reviewTimes or ''}}"
                               id="reviewTimes">
                    </div>
                </div>
                <div class="form-group col-xs-12 col-md-3">
                    <input id="publish" name="publish" type="checkbox" value="1" {{empty($form->publish)?'':'checked'}}>
                    <label for="publish" class="control-label">是否发布 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="只有表单发布，其他人才可以填写此表单"></span></label>
                </div>
                {{--<div class="form-group col-xs-12 col-md-2">--}}
                {{--<input id="filterBlacklist" name="filterBlacklist" type="checkbox"--}}
                {{--value="1" {{empty($form->filterBlacklist)?'':'checked'}}>--}}
                {{--<label for="filterBlacklist" class="control-label">是否过滤黑名单</label>--}}
                {{--</div>--}}
                <div class="form-group col-xs-12 col-md-3">
                    <input id="showReview" name="showReview" type="checkbox"
                           value="1" {{empty($form->showReview)?'':'checked'}}>
                    <label for="showReview" class="control-label">展示审核意见 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="将管理员审批的审核意见展示于用户填写的表单末尾"></span></label>
                </div>
                <div class="form-group col-xs-12 col-md-3" >
                    <input id="showRemark" name="showRemark" type="checkbox"
                           value="1" {{empty($form->showRemark)?'':'checked'}}>
                    <label for="showRemark" class="control-label">展示审核评论 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="将管理员审批的评论展示于用户填写的表单末尾"></span></label>
                </div>
                <input id="fieldNames" name="fieldNames" type="hidden"/>
                <input id="fields" value="{{$form->fields or '[]'}}" name="fields" type="hidden"/>
            </form>
            <div class="form-group col-xs-12 col-md-12" style="font-size:12px;">
                <div id="build-wrap"></div>
            </div>
            <button class="btn-cancel btn btn-default btn-wide col-xs-12 col-sm-4" type="button">取消</button>
            <button class="btn-preview btn btn-info btn-wide col-xs-12 col-sm-4" type="button">预览</button>
            <button class="btn-submit btn btn-primary btn-wide col-xs-12 col-sm-4" type="button" id="submit">提交</button>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/node_modules/jqueryui/jquery-ui.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/formBuilder/dist/form-builder.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/formBuilder/dist/form-render.min.js')}}"></script>
    <script src="{{cdnAsset('/assets/js/jquery.formautofill.min.js')}}"></script>
    <script>
        $('.tooltip').tooltip();
        $(':radio').radiocheck();
        $(':checkbox').bootstrapSwitch();
        $("select").select2({dropdownCssClass: 'dropdown-inverse'});
        @if($method=='PUT')
            $('#nav-edit').addClass('active');
        @endif

    jQuery(function ($) {
            var $fieldsData = JSON.parse($('#fields').val());
            var options = {
                i18n: {
                    locale: 'zh-CN'
                },
                defaultFields: $fieldsData,
                controlOrder: [
                    'text',
                    'textarea'
                ],
                dataType: 'json',
                disableFields: ['autocomplete', 'button', 'header'],
                editOnAdd: true,
                sortableControls: true,
                showActionButtons: false
            };
            var $formBuilder = $('#build-wrap').formBuilder(options);

            $('.btn-submit').click(function () {
                var $jsonStr = $formBuilder.formData;
                $('#fields').val($jsonStr);
                var $fieldNames = [];
                var $jsonObj = JSON.parse($jsonStr);
                $.each($jsonObj, function (i, item) {
                    if (item.name != undefined && item.name != null) {
                        $fieldNames.push(item.name);
                    }
                });
                $('#fieldNames').val(JSON.stringify($fieldNames));
                $('form').submit();
            });
            $('.btn-preview').click(function () {
                var $jsonStr = $formBuilder.formData;
                if ($jsonStr == '[]') {
                    toastr.error('请添加内容');
                    return;
                }
                var w = window.open();
                $.post('/web/form/preview', {fields: $jsonStr}, function (data) {
                    var id = data.id;
                    w.location = '/web/form/preview/' + id;
                })
            });
            $('.btn-cancel').click(function () {
                window.location = '/web/form';
            });
        });
    </script>
@endsection
