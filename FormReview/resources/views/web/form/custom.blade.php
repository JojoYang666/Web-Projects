@extends('layouts.app')
@section('css')
    <link href="{{cdnAsset('/node_modules/bootstrap-fileinput/css/fileinput.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="container">
        @include('web.form.nav')
        @if($form->customView)
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>状态：</strong>{{\App\Form::$customStatus[$form->customStatus]}}
            @if($form->customStatus==\App\Form::REVIEW_REFUSE)
                <span class="label label-warning">{{$form->customReview}}</span>
            @endif
        </div>
        @endif
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            如果您想自定义表单样式，可以使用此功能。<br>
            上传写好的HTML文件，即自定义表单样式的HTML代码，审核通过后即可展示您自定义的表单样式！<hr>
            <strong>警告!</strong> <br>
            1. 请上传完整的HTML文件！HTML页中的静态文件链接可以通过下方静态文件上传得到链接，需手动替换！<br>
            2. 请将表单提交地址设置为<span class="label label-info">{{route('pub.info.store', $form->id)}}</span>，提交方法设置为POST！<br>
            3. HTML文件中的name属性可以在下方设置，注意：会覆盖现有表单结构！<br>
            4. 上传成功后请等待审核，审核通过方可展示自定义样式！<br>
            5. 由于自定义页面js可能不兼容，所以现不能在自定义页面编辑已填表单！
        </div>
        <div class="form-group col-xs-12 col-md-12">
            <label for="assets" class="control-label">静态文件</label>
            <input name="assets[]" id="assets" type="file" class="file" multiple>
        </div>
        <div class="form-group col-xs-12 col-md-12">
            <label for="html" class="control-label">HTML文件</label>
            <input type="file" name="html" id="html" class="file">
        </div>
        <div class="form-group col-xs-12 col-md-12">
            <label for="html" class="control-label">表单域</label>
            <div class="tagsinput-primary">
                <input id="filed" name="filed" class="tagsinput" data-role="tagsinput" value="{{$fieldsStr or ''}}" placeholder="格式为：域名|name值，如：手机|phone"/>
                <button class="btn btn-default" onclick="saveFiled()">保存</button>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/node_modules/bootstrap-fileinput/js/fileinput.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/bootstrap-fileinput/js/locales/zh.js')}}"></script>
    <script>
        var $fid='{{$form->id}}';
        $('#nav-custom').addClass('active');
        $("#assets").fileinput({
            maxFileSize:1000,
            uploadUrl: '/web/form/upload/assets'
        });
        $("#html").fileinput({
            showPreview:false,
            maxFileSize:1000,
            allowedFileTypes:['html', 'text'],
            uploadUrl: '/web/form/upload/html',
            uploadExtraData:{id:$fid}
        });
        $('#html').on('fileuploaded', function(event, data, previewId, index) {
            toastr.success('文件已上传，请等待审核！');
            window.location.reload();
        });
        function saveFiled() {
            var $input=$('#filed');
            $.post('/web/form/'+$fid+'/saveFiled',{
                field:$input.val()
            },function (data) {
                if(data.status==0) {
                    toastr.success('更新成功');
                }else {
                    toastr.error('更新失败,'+data.error);
                }
            });
        }
    </script>
@endsection