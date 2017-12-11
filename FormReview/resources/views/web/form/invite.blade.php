@extends('layouts.app')

@section('content')
    {{--使用jquery实现版本，固定3个条件--}}
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">邀请管理员</div>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form" action="{{route('web.admin.store')}}" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group">

                                <label for="form" class="col-sm-2 control-label">表单</label>
                                <div class="col-sm-10">
                                    <select id="mytable" name="tableId"
                                            class="form-control select select-primary select-block mbl">
                                        <option data-fields='' value="">请选择表单</option>
                                        @foreach($forms as $form)
                                            <option data-fields='{{$form->fields}}'
                                                    data-times='{{$form->reviewTimes}}'
                                                    value="{{ $form->id }}">{{ $form->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('tableId'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('tableId') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="authorities" class="col-sm-2 control-label">权限</label>
                                <div class="col-sm-10">
                                    <select name="authorities[]" multiple="multiple"
                                            class="form-control multiselect multiselect-info" required>
                                        @foreach(\App\Form::$authorities as $key => $authority)
                                            <option value="{{ $key }}">{{ $authority }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('authorities'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('authorities') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="stage" class="col-sm-2 control-label">审核阶段</label>
                                <div class="col-sm-10">
                                    <select id="stage" name="stages[]" multiple="multiple"
                                            class="form-control multiselect multiselect-info" required>
                                    </select>
                                    @if ($errors->has('stage'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('stage') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="add_condition" class="col-sm-2 control-label">是否添加过滤条件</label>
                                <div class="col-sm-10">
                                    <div class="bootstrap-switch-square">
                                        <input class="add_condition" type="checkbox"
                                               data-on-text="<i class='fui-check'></i>"
                                               data-off-text="<i class='fui-cross'></i>" name="add_condition"
                                               id="condition" title="condition"/>
                                    </div>
                                </div>
                            </div>
                            <div class="condition">
                                <div class="form-group conditions condition1">
                                    <label for="users" class="col-sm-2 control-label">条件1</label>
                                    <div class="col-sm-3">
                                        <select name="key1"
                                                class="form-control select select-primary select-block mbl condition_key condition_key0">
                                            <option value="">请选择表单项</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-7">
                                        <input name="value1" class="tagsinput condition_value" data-role="tagsinput"
                                               value="{{old('value1')}}" placeholder="多个值用“,”分隔"/>
                                    </div>
                                </div>
                                <div class="form-group conditions">
                                    <label for="users" class="col-sm-2 control-label">条件2</label>
                                    <div class="col-sm-3">
                                        <select name="key2"
                                                class="form-control select select-primary select-block mbl condition_key condition_key1">
                                            <option value="">请选择表单项</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-7">
                                        <input name="value2" class="tagsinput condition_value" data-role="tagsinput"
                                               value="{{old('value2')}}" placeholder="多个值用“,”分隔"/>
                                    </div>
                                </div>
                                <div class="form-group conditions">
                                    <label for="users" class="col-sm-2 control-label">条件3</label>
                                    <div class="col-sm-3">
                                        <select name="key3"
                                                class="form-control select select-primary select-block mbl condition_key condition_key2">
                                            <option value="">请选择表单项</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-7">
                                        <input name="value3" class="tagsinput condition_value" data-role="tagsinput"
                                               value="{{old('value3')}}" placeholder="多个值用“,”分隔"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="users" class="col-sm-2 control-label">用户</label>
                                <div class="col-sm-10">
                                    <input name="users" id="users" type="text" class="form-control"
                                           data-role="tagsinput" placeholder="多个用户用“,”分隔" required>
                                    @if ($errors->has('users'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('users') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-10">
                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-default">发送邀请</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
//        ToDO 很大问题
        var users = new Bloodhound({
            prefetch: '/web/admin/getExceptUsers',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        users.initialize();
        $('#users').tagsinput({
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                displayKey: 'name',
                source: users.ttAdapter()
            }
        });

        $(document).ready(function () {
            $(':radio').radiocheck();
            $(':checkbox').bootstrapSwitch();
            $("select").select2({dropdownCssClass: 'dropdown-inverse'});

            $("#mytable").change(function () {
                var fields = $(this).children("option:selected").attr('data-fields');
                var reviewTimes = $(this).children("option:selected").attr('data-times');
                $(".condition_key").empty();
                $('.condition_key').append('<option value="">请选择表单项</option>');
                if (fields) {
                    fields = jQuery.parseJSON(fields);
                    $.each(fields, function (index, element) {
                        $(".condition_key").append('<option data-type="' + element.type + '" data-options=\'' + JSON.stringify(element.options) + '\' value="' + element.name + '">' + element.label + '</option>');
                    });
                }
                var $stageSel = $('#stage');
                $stageSel.empty();
                for(var i=0;i<reviewTimes;i++) {
                    $stageSel.append('<option value="'+i+'">'+reviewTime2zh(i)+'</option>');
                }
                $("select").select2({dropdownCssClass: 'dropdown-inverse'});
            });

            $("#add_condition").on('change.radiocheck', function () {
                console.log('add_condition');
                if ($(this).prop('checked') == true) {
                    $(".condition:eq(0)").show();
                } else {
                    $(".condition:eq(0)").hide();
                }
            });

            $('.condition_key').change(function () {
                var selectedOption = $(this).children("option:selected");
                var type = selectedOption.attr('data-type');
                if (type) {
                    var options = selectedOption.attr('data-options');
                    if (options && options != 'undefined' && typeof(options) !== "undefined") {
                        console.log(typeof(options) !== "undefined");
                        options = jQuery.parseJSON(options);
                        console.log(options);
                        var ops = new Bloodhound({
                            local: options,
                            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                            queryTokenizer: Bloodhound.tokenizers.whitespace
                        });
                        ops.initialize();
                        $(this).parent().next().children().tagsinput('refresh');
                        $(this).parent().next().children().tagsinput({
                            maxChars: 20,
                            itemValue: 'key',
                            itemText: 'value',
                            typeaheadjs: {
                                displayKey: 'value',
                                source: ops.ttAdapter()
                            }
                        });
                    }
                    /*else {
                     $(this).parent().next().children().tagsinput('refresh');
                     }*/
                }
            });

            var $formId = getQueryString('formId');
            $("#mytable").val($formId).trigger("change")
        });

    </script>
@endsection
