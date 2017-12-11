@extends('layouts.app')

@section('content')
    <div class="container">
        @include('web.form.nav')
        <div class="row form-overview">
            <div class="col-xs-6 col-md-3">
                <div class="well well-lg">
                    <span class="number">
                        <a href="{{route('web.form.datalist',$form->id)}}">{{$summary['totalCount']}}</a>
                    </span>
                    <span class="info-name">表单总数据</span>
                </div>
            </div>
            <div class="col-xs-6 col-md-3">
                <div class="well well-lg">
                    <span class="number">
                        <a href="{{route('web.form.datalist',['id'=>$form->id,'today'=>1])}}">{{$summary['todayCount']}}</a>
                    </span>
                    <span class="info-name">今日提交</span>
                </div>
            </div>
            <div class="col-xs-6 col-md-3">
                <div class="well well-lg">
                    <span class="number">
                        <a href="{{route('web.form.datalist',['id'=>$form->id,'noReview'=>1])}}">{{$summary['noReviewCount']}}</a>
                    </span>
                    <span class="info-name">未审核</span>
                </div>
            </div>
            <div class="col-xs-6 col-md-3">
                <div class="well well-lg">
                    <span class="number">
                        <a href="{{route('web.form.datalist',['id'=>$form->id,'hasReview'=>1])}}">{{$summary['hasReviewCount']}}</a>
                    </span>
                    <span class="info-name">已审核</span>
                </div>
            </div>
        </div>
        @if($canUpdate)
            <div class="row">
                <div class="form-group col-xs-12 col-md-3">
                    <input class="ajax-update" id="publish" name="publish" type="checkbox"
                           value="1" {{empty($form->publish)?'':'checked'}}>
                    <label for="publish" class="control-label">发布 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="只有表单发布，其他人才可以填写此表单"></span></label>
                </div>
                {{--<div class="form-group col-xs-12 col-md-3">
                    <input class="ajax-update" id="filterBlacklist" name="filterBlacklist" type="checkbox"
                           value="1" {{empty($form->filterBlacklist)?'':'checked'}}>
                    <label for="filterBlacklist" class="control-label">过滤黑名单</label>
                </div>--}}
                <div class="form-group col-xs-12 col-md-3">
                    <input class="ajax-update" id="showReview" name="showReview" type="checkbox"
                           value="1" {{empty($form->showReview)?'':'checked'}}>
                    <label for="showReview" class="control-label">展示审核意见 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="将管理员审批的审核意见展示于用户填写的表单末尾"></span></label>
                </div>
                <div class="form-group col-xs-12 col-md-3">
                    <input class="ajax-update" id="showRemark" name="showRemark" type="checkbox"
                           value="1" {{empty($form->showRemark)?'':'checked'}}>
                    <label for="showRemark" class="control-label">展示审核评论 <span class="glyphicon glyphicon-question-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="将管理员审批的评论展示于用户填写的表单末尾"></span></label>
                </div>
                {{--自定义样式--}}
                @if($form->customStatus==\App\Form::REVIEW_PASS or $form->customStatus==\App\Form::CUSTOM_STOP)
                    <div class="form-group col-xs-12 col-md-3">
                        <input class="ajax-update" id="customStatus" name="customStatus" type="checkbox"
                               value="{{\App\Form::REVIEW_PASS}}" {{$form->customStatus==\App\Form::REVIEW_PASS ?'checked':''}}>
                        <label for="customStatus" class="control-label">使用自定义样式</label>
                    </div>
                @endif
            </div>
            <div class="row">
                {{--表单填写限制--}}
                <div class="form-limit form-group col-xs-12 col-md-3">
                    <button class="btn btn-default" data-toggle="modal" data-target=".form-limit-modal">表单填写限制
                    </button>
                    <div class="modal fade form-limit-modal" tabindex="-1" role="dialog"
                         aria-labelledby="mySmallModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">表单填写限制</h4>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                            <label for="limitTimes" class="col-sm-2 control-label">限制次数</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="limitTimes"
                                                       value="{{$form->limitTimes}}" placeholder="每个人提交表单次数，-1表示不限制">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPassword3" class="col-sm-2 control-label">依据</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select select-primary select-block mbl"
                                                        id="limitBy">
                                                    <optgroup label="">
                                                        <option value="ip" {{$form->limitBy=="ip"?'selected':''}}>IP
                                                        </option>
                                                        @if($form->wechat)
                                                            <option value="openid" {{$form->limitBy=="openid"?'selected':''}}>
                                                                微信OPENID
                                                            </option>
                                                        @endif
                                                    </optgroup>
                                                    <optgroup label="表单域">
                                                        @foreach(json_decode($form->fields) as $field)
                                                            @if(isset($field->name))
                                                            <option value="{{$field->name}}" {{$form->limitBy==$field->name?'selected':''}}>{{$field->label}}</option>
                                                            @endif
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-primary btn-form-limit">确认</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        @if($form->creator==Auth::user()->id)
            <div class="row">
                {{--删除表单--}}
                <div class="form-delete form-group col-xs-12 col-md-3">
                    <button class="btn btn-danger" data-toggle="modal" data-target=".form-delete-modal-sm">删除表单</button>
                    <div class="modal fade form-delete-modal-sm" tabindex="-1" role="dialog"
                         aria-labelledby="mySmallModalLabel">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">删除表单</h4>
                                </div>
                                <div class="modal-body">
                                    确认删除？
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-primary btn-form-delete">确认</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                {{--解除表单管理--}}
                <div class="form-delete form-group col-xs-12 col-md-3">
                    <button class="btn btn-danger" data-toggle="modal" data-target=".form-cancel-modal-sm">解除表单管理</button>
                    <div class="modal fade form-cancel-modal-sm" tabindex="-1" role="dialog"
                         aria-labelledby="mySmallModalLabel">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">解除表单管理</h4>
                                </div>
                                <div class="modal-body">
                                    确认解除表单管理？
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                    <button type="button" class="btn btn-primary btn-form-cancel">确认</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/node_modules/bootstrap-checkbox/dist/js/bootstrap-checkbox.min.js')}}"></script>
    {{--<script src="//cdn.bootcss.com/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>--}}
    <script>
        var formId = '{{$form->id}}';
        $('#nav-index').addClass('active');
        $("select").select2({dropdownCssClass: 'dropdown-inverse'});
        $(function () {
            $(':checkbox').checkboxpicker().on('change', function () {
                var $name = $(this).attr('name');
                var $value = $(this).val();
                $.post('/web/form/ajax-update', {
                    id: formId,
                    name: $name,
                    value: $(this).prop('checked')
                }, function (data) {
                    if (data.status != 0) {
                        toastr.error(data.error);
                    } else {
                        toastr.success('更改成功！');
                    }
                });
            });
            $('.btn-form-delete').click(function () {
                $.ajax({
                    url: '/web/form/' + formId,
                    type: 'DELETE',
                    data: {}
                });
                window.location = '/web/form';
            });
            $('.btn-form-cancel').click(function () {
                $.get('/web/form/'+formId+'/cancel',function (data) {
                    if(data.status==0) {
                        toastr.success('解除成功！');
                        window.location = '/web/form';
                    }else {
                        toastr.error('解除失败！');
                    }
                });
            });
            $('.btn-form-limit').click(function () {
                $.post('/web/form/limit', {
                    id: formId,
                    limitTimes: $('#limitTimes').val(),
                    limitBy: $('#limitBy').val()
                }, function (data) {
                    if (data.status == 0) {
                        toastr.success('保存成功');
                        $('.form-limit-modal').modal('hide')
                    } else {
                        toastr.error(data.error);
                    }
                });
            });
        });
    </script>
@endsection