@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">邀请管理员</div>

                    <div class="panel-body">
                        <form class="form-horizontal" role="form" action="{{url('/web/form/admin')}}" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group">

                                <label for="form" class="col-sm-2 control-label">表单</label>
                                <div class="col-sm-10">
                                    <select id="mytable" name="tableId"
                                            class="form-control select select-primary select-block mbl">
                                        <option data-fields='' value="">请选择表单</option>
                                        @foreach($forms as $form)
                                            <option data-fields='{{$form->fields}}'
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
                                    <select multiple="multiple" class="form-control multiselect multiselect-info">
                                        @foreach(\App\Form::$authorities as $key => $authority)
                                            <option value="{{ $key }}">{{ $authority }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="add_condition" class="col-sm-2 control-label">是否添加过滤条件</label>
                                <div class="col-sm-10">
                                    <div class="bootstrap-switch-square">
                                        <input class="add_condition" type="checkbox"
                                               data-on-text="<i class='fui-check'></i>"
                                               data-off-text="<i class='fui-cross'></i>" name="add_condition"
                                               id="condition"/>
                                    </div>
                                </div>
                            </div>
                            <div class="condition" style="display: none">
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
                                               value="" placeholder="多个值用“,”分隔"/>
                                    </div>
                                </div>
                                {{--<div class="form-group conditions">--}}
                                    {{--<label for="users" class="col-sm-2 control-label">条件2</label>--}}
                                    {{--<div class="col-sm-3">--}}
                                        {{--<select name="key2"--}}
                                                {{--class="form-control select select-primary select-block mbl condition_key condition_key1">--}}
                                            {{--<option value="">请选择表单项</option>--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-7">--}}
                                        {{--<input name="value2" class="tagsinput condition_value" data-role="tagsinput"--}}
                                               {{--data-values="" placeholder="多个值用“,”分隔"/>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" class="btn btn-default col-xs-3" id="add_condition">增加条件
                                        </button>
                                        <button type="button" class="btn btn-default col-xs-3" id="del_condition"
                                                style="display: none">删除条件
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="users" class="col-sm-2 control-label">用户</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="users" placeholder="多个用户用“,”分隔">
                                    @if ($errors->has('users'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('users') }}</strong>
                                        </span>
                                    @endif
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

        var citynames = new Bloodhound({
            local: ['Amsterdam', 'Washington', 'Sydney', 'Beijing', 'Cairo'],
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });
        citynames.initialize();
        $('.condition_value').tagsinput({
            maxTags: 3,
            maxChars: 20,
            typeaheadjs: {
                source: citynames.ttAdapter()
            }
        });

        $(document).ready(function () {
            $(':radio').radiocheck();
            $(':checkbox').bootstrapSwitch();
            $("select").select2({dropdownCssClass: 'dropdown-inverse'});


            //显示错误
//            var error = '';
//            error = error.replace(/<\/?[^>]*>/g, '');
//            if (error) {
//                toastr.warning(error);
//            }

            var addflg1 = 1;

            function addOption(fields, addflg1) {
                $(".condition_key"+addflg1).empty();
                $.each(fields, function (index, element) {
                    $(".condition_key"+addflg1).append('<option data-type="' + element.type + '" data-options=\'' + JSON.stringify(element.options) + '\' value="' + element.name + '">' + element.label + '</option>');
                });
                $("select").select2({dropdownCssClass: 'dropdown-inverse'});
            }

            $("#mytable").change(function () {
                var fields = jQuery.parseJSON($(this).children("option:selected").attr('data-fields'));
                console.log(fields);
                addflg1 = -1;
                $("#del_condition").hide();
                $(".conditions").remove();
                $("#add_condition").click();
                addOption(fields, 0);
            });

            $(".add_condition").change(function () {
                if($(this).prop('checked')==true){
                    $(".condition:eq(0)").show();
                }else {
                    $(".condition:eq(0)").hide();
                }
            });

            $('.condition_key0').change(function () {
                var selectedOption = $(this).children("option:selected");
                var conditionIndex = $(this).prop('name').substring(3);
                var type = selectedOption.attr('data-type');
                if (type) {
                    var types = ['dropdown', 'multiselect', 'checkbox', 'radio'];
                    if ($.inArray(type, types) > 0) {
                        var options = jQuery.parseJSON(selectedOption.attr('data-options'));
                        var selectHtml = '<select name="value' + conditionIndex + '" class="condition_key" data-am-selected="{btnWidth: 100%}">';
                        $.each(options, function (index, item) {
                            selectHtml += '<option value="' + index + '">' + item + '</option>';
                        });
                        selectHtml += '</select>';
                        $(this).parent().next().html(selectHtml);
                    }
                }
            });


            $("#add_condition").click(function () {
                addflg1++;
                $('<div class="form-group conditions condition' + addflg1 + '">' +
                        '<label for="role" class="am-u-sm-3 am-form-label">条件</label>' +
                        '<div class="am-u-sm-3">' +
                        '<select name="key' + addflg1 + '" class="condition_key condition_key' + addflg1 + '" data-am-selected="{btnWidth: 100%}">' +
                        '<option value="">请选择表单项</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="am-u-sm-6">' +
                        '<input type="text" name="value' + addflg1 + '" class="condition_value" placeholder="多个值用“,”分隔">' +
                        '</div>' +
                        '</div>').insertBefore(".add_btn");
                if (addflg1 == 1) {
                    $("#del_condition").show();
                }
                addOption(addflg1);

                $('.condition_key' + addflg1).change(function () {
                    var selectedOption = $(this).children("option:selected");
                    var conditionIndex = $(this).prop('name').substring(3);
                    var type = selectedOption.attr('data-type');
                    if (type) {
                        var types = ['dropdown', 'multiselect', 'checkbox', 'radio'];
                        if ($.inArray(type, types) > 0) {
                            var options = jQuery.parseJSON(selectedOption.attr('data-options'));
                            var selectHtml = '<select name="value' + conditionIndex + '" class="condition_key" data-am-selected="{btnWidth: 100%}">';
                            $.each(options, function (index, item) {
                                selectHtml += '<option value="' + index + '">' + item + '</option>';
                            });
                            selectHtml += '</select>';
                            $(this).parent().next().html(selectHtml);
                        }
                        else {
                            $(this).parent().next().html("<input type='text' name='value" + conditionIndex + "' id='doc-ipt-pwd-2' placeholder='多个值用“,”分隔'><small><span style='color: red'></span></small>");
                        }
                    }
                });
            });

            $("#del_condition").click(function () {
                $(".condition" + addflg1).remove();
                addflg1--;
                if (addflg1 < 1) {
                    $("#del_condition").hide();
                }
            });

        });
    </script>
@endsection
