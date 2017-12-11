@extends('layouts.app')

@section('content')
    <div class="container">
        @include('web.form.nav')

        <div class="panel-group" id="notices" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default notice-panel">
                <div class="panel-heading notice-heading" role="tab" id="headingWechat" data-fid="{{$form->id}}"
                     data-type="{{\App\Notice::TYPE_REVIEW}}" data-way="{{\App\Notice::WAY_WECHAT}}">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#notices" href="#wechat-collapse"
                           aria-expanded="false" aria-controls="wechat-collapse">
                            审核微信通知
                        </a>
                    </h4>
                </div>
                <div id="wechat-collapse" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingWechat">
                    <div class="panel-body">
                        @if($form->wechat)
                            <form class="form-horizontal" role="form" method="post"
                                  action="{{route('web.form.saveNotice',$form->id)}}">
                                {!! csrf_field() !!}
                                <input type="hidden" name="type" value="{{\App\Notice::TYPE_REVIEW}}">
                                <input type="hidden" name="way" value="{{\App\Notice::WAY_WECHAT}}">
                                <div class="form-group">
                                    <label for="status" class="col-sm-2 control-label">发送通知</label>
                                    <div class="col-sm-10"><input class="notice-status" type="checkbox" name="status"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="template_id" class="col-sm-2 control-label">模板ID</label>
                                    <div class="col-sm-10">
                                        <input name="template_id" type="text" placeholder="模板ID" class="form-control"
                                               required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="template_keys" class="col-sm-2 control-label">模板关键词</label>
                                    <div class="col-sm-10">
                                        <input id="template_keys1" name="template_keys" class="tagsinput template_keys"
                                               placeholder="输入逗号分隔" onchange="refreshKeys(1)"
                                               data-role="tagsinput" value=""/>
                                    </div>
                                </div>
                                <div class="reviews" data-type="1" data-times="{{$form->reviewTimes}}"></div>

                                <hr>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-default">保存</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            此表单还没有绑定微信平台，<a href="{{ route('web.form.edit',$form->id) }}">去绑定</a>@endif
                    </div>
                </div>
            </div>
            <div class="panel panel-default notice-panel">
                <div class="panel-heading notice-heading" role="tab" id="heading-admin-wechat" data-fid="{{$form->id}}"
                     data-type="{{\App\Notice::TYPE_ADMIN}}" data-way="{{\App\Notice::WAY_WECHAT}}">
                    <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#notices" href="#admin-wechat-collapse"
                           aria-expanded="false" aria-controls="admin-wechat-collapse">
                            管理员微信通知
                        </a>
                    </h4>
                </div>
                <div id="admin-wechat-collapse" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="heading-admin-wechat">
                    <div class="panel-body">
                        @if($form->wechat)
                            <form class="form-horizontal" role="form" method="post"
                                  action="{{route('web.form.saveNotice',$form->id)}}">
                                {!! csrf_field() !!}
                                <input type="hidden" name="type" value="{{\App\Notice::TYPE_ADMIN}}">
                                <input type="hidden" name="way" value="{{\App\Notice::WAY_WECHAT}}">
                                <div class="form-group">
                                    <label for="status" class="col-sm-2 control-label">发送通知</label>
                                    <div class="col-sm-10"><input class="notice-status" type="checkbox" name="status"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="template_id" class="col-sm-2 control-label">模板ID</label>
                                    <div class="col-sm-10">
                                        <input name="template_id" type="text" placeholder="模板ID" class="form-control"
                                               required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="template_keys" class="col-sm-2 control-label">模板关键词</label>
                                    <div class="col-sm-10">
                                        <input id="template_keys2" name="template_keys" class="tagsinput template_keys"
                                               placeholder="输入逗号分隔" onchange="refreshKeys(2)"
                                               data-role="tagsinput" value=""/>
                                    </div>
                                </div>
                                <div class="reviews" data-type="2" data-times="{{$form->reviewTimes}}"></div>

                                <hr>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-default">保存</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            此表单还没有绑定微信平台，<a href="{{ route('web.form.edit',$form->id) }}">去绑定</a>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src='{{cdnAsset('node_modules/spectrum-colorpicker/spectrum.js')}}'></script>
    <link rel='stylesheet' href='{{cdnAsset('node_modules/spectrum-colorpicker/spectrum.css')}}'/>
    <script src="{{cdnAsset('/assets/js/jquery.formautofill.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/bootstrap-checkbox/dist/js/bootstrap-checkbox.min.js')}}"></script>
    <script>
        $('#nav-setting').addClass('active');
        $("select").select2({dropdownCssClass: 'dropdown-inverse'});
        $('.tooltip').tooltip();
        $(':checkbox').checkboxpicker();

        function refreshKeys($type) {
            var $keys = $('#template_keys'+$type).tagsinput('items');
            var $keysDivs = $('.template-keys'+$type);
            $.each($keysDivs, function (index, item) {
                var $keysDiv = $(item);
                var $keysTimes = $keysDiv.attr('data-reviewTimes');
                var $keysStatus = $keysDiv.attr('data-status');
                var $keyDivHtml = '';
                $.each($keys, function (i, $key) {
                    $keyDivHtml += '<div class="form-group">' +
                        '<label for="review_' + $keysTimes + '_' + $keysStatus + '_' + $key + '" class="col-sm-2 control-label" >' + $key + ' <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="可使用{EVALUATION}插入审核意见，{CUSTOM}插入审核时自定义内容，{NICKNAME}插入用户微信昵称，{status}插入表单状态，{表单域的name值}插入用户填写该表单域的值，{TIME}插入表单填写日期，<br>换行"></span> </label>' +
                        '<div class="col-sm-10">' +
                        '<div class="form-inline">' +
                        '<input name="review_' + $keysTimes + '_' + $keysStatus + '_' + $key + '_value" type="text" class="form-control notice-key-value" placeholder="" style="width: 90%"/>' +
                        '<input name="review_' + $keysTimes + '_' + $keysStatus + '_' + $key + '_color" type="text" class="form-control colorpicker" value="#000000" />' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                });
                $keysDiv.html($keyDivHtml);
                $('[data-toggle="tooltip"]').tooltip()
            });

            $(".colorpicker").spectrum({
                preferredFormat: "hex"
            });
        }

        /**
         * 加载数据
         * @param $fid
         * @param $type
         * @param $way
         * @param $form
         */
        function loadData($fid, $type, $way, $form) {
            $.get('/web/form/' + $fid + '/getNotice', {
                type: $type,
                way: $way
            }, function (data, status) {
                if (data.length != 0) {
                    var filldata = {};
                    filldata['platform_id'] = data.data.platform_id;
                    filldata['template_id'] = data.data.template_id;
                    filldata['template_keys'] = data.data.template_keys.toString();
                    $('#template_keys'+$type).tagsinput('add', filldata['template_keys']);
                    $.each(data.data.review, function (i, item) {
                        filldata['review_' + i + '_pass_url'] = item.pass.url;
                        filldata['review_' + i + '_pass_topcolor'] = item.pass.topcolor;
                        $.each(item.pass.data, function (k, kdata) {
                            filldata['review_' + i + '_pass_' + k + '_color'] = kdata.color;
                            filldata['review_' + i + '_pass_' + k + '_value'] = kdata.value;
                        });
                        filldata['review_' + i + '_refuse_url'] = item.refuse.url;
                        filldata['review_' + i + '_refuse_topcolor'] = item.refuse.topcolor;
                        $.each(item.refuse.data, function (k, kdata) {
                            filldata['review_' + i + '_refuse_' + k + '_color'] = kdata.color;
                            filldata['review_' + i + '_refuse_' + k + '_value'] = kdata.value;
                        });
                    });
                    var autofillOpts = {
                        restrict: false,
                        findbyname: true
                    };
                    $form.autofill(filldata, autofillOpts);

                    console.log(data.status);
                    $form.children().find('.notice-status').prop('checked', data.status?'checked':'');
                    $(".colorpicker").spectrum({
                        preferredFormat: "hex"
                    });
                }
            });
        }
        $(function () {
            var $reviewDivs = $('.reviews');
            $.each($reviewDivs,function (i, reviewDiv) {
                var $reviewTimes = $(reviewDiv).attr('data-times');
                var $reviewType = $(reviewDiv).attr('data-type');
                var $reviewDivHtml = '';
                if($reviewType==1) {
                    for (var $t = 1; $t <= $reviewTimes; $t++) {
                        $reviewDivHtml += '<hr>' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '" class="col-sm-2 control-label">第' + $t + '次审核</label>' +
                            '</div>' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_pass" class="col-sm-2 control-label">通过</label>' +
                            '<div class="col-sm-10">' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_pass_topcolor" class="col-sm-2 control-label">标题颜色</label>' +
                            '<div class="col-sm-10">' +
                            '<input name="review_' + $t + '_pass_topcolor" type="text" class="form-control colorpicker" value="#000000"/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="template-keys template-keys'+$reviewType+'" data-reviewTimes="' + $t + '" data-status="pass"></div>' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_pass_url" class="col-sm-2 control-label">URL <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="点击模板跳转链接,输入{USER_LINK}跳转到用户填写的表单页面"></span></label>' +
                            '<div class="col-sm-10">' +
                            '<input name="review_' + $t + '_pass_url" type="text" class="form-control" placeholder="请输入模板跳转链接"/>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_refuse" class="col-sm-2 control-label">拒绝</label>' +
                            '<div class="col-sm-10">' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_refuse_topcolor" class="col-sm-2 control-label">标题颜色</label>' +
                            '<div class="col-sm-10">' +
                            '<input name="review_' + $t + '_refuse_topcolor" type="text" class="form-control colorpicker" value="#000000"/>' +
                            '</div>' +
                            '</div>' +
                            '<div class="template-keys template-keys'+$reviewType+'" data-reviewTimes="' + $t + '" data-status="refuse"></div>' +
                            '<div class="form-group">' +
                            '<label for="review_' + $t + '_refuse_url" class="col-sm-2 control-label">URL <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="点击模板跳转链接,输入{USER_LINK}跳转到用户填写的表单页面"></span></label>' +
                            '<div class="col-sm-10">' +
                            '<input name="review_' + $t + '_refuse_url" type="text" class="form-control" placeholder="请输入模板跳转链接"/>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    }
                }else if($reviewType==2) {
                    //管理员通知不需要分审核次数和通过拒绝，但是为了和审核微信通知保持一直，暂定为第一次审核通过的情况
                    $t=1;
                    $reviewDivHtml += '<hr>' +
                        '<div class="form-group">' +
                        '<div class="col-sm-10">' +
                        '<div class="form-group">' +
                        '<label for="review_' + $t + '_pass_topcolor" class="col-sm-2 control-label">标题颜色</label>' +
                        '<div class="col-sm-10">' +
                        '<input name="review_' + $t + '_pass_topcolor" type="text" class="form-control colorpicker" value="#000000"/>' +
                        '</div>' +
                        '</div>' +
                        '<div class="template-keys template-keys'+$reviewType+'" data-reviewTimes="' + $t + '" data-status="pass"></div>' +
                        '<div class="form-group">' +
                        '<label for="review_' + $t + '_pass_url" class="col-sm-2 control-label">URL <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="点击模板跳转链接,输入{USER_LINK}跳转到用户填写的表单页面"></span></label>' +
                        '<div class="col-sm-10">' +
                        '<input name="review_' + $t + '_pass_url" type="text" class="form-control" placeholder="请输入模板跳转链接"/>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                }

                $(reviewDiv).html($reviewDivHtml);

                refreshKeys($reviewType);
            });


            $(".colorpicker").spectrum({
                preferredFormat: "hex"
            });

            $('.notice-panel').on('shown.bs.collapse', function () {
                var heading = $(this).children('.notice-heading');
                var form = $(this).children().find('form');
                var fid = heading.attr('data-fid'), type = heading.attr('data-type'), way = heading.attr('data-way');
                loadData(fid, type, way, form);
            })
        });
    </script>
@endsection