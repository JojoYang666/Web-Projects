@extends('layouts.app')

@section('content')
    <div class="container">
        @include('web.form.nav')
        <div id="toolbar" class="btn-group">
            <a id="btn_add" type="button" class="btn btn-default" href="{{route('web.form.datalist',$form->id)}}">总数据</a>
            <a id="btn_add" type="button" class="btn btn-default" href="{{route('web.form.datalist',['id'=>$form->id,'today'=>1])}}">今日提交</a>
            <a id="btn_add" type="button" class="btn btn-default" href="{{route('web.form.datalist',['id'=>$form->id,'noReview'=>1])}}">未审核</a>
            <a id="btn_add" type="button" class="btn btn-default" href="{{route('web.form.datalist',['id'=>$form->id,'hasReview'=>1])}}">已审核</a>
        </div>
        <table id="table" data-toolbar="#toolbar" data-reviewtimes="{{$reviewTimes}}"
               data-show-pagination-switch="true">
        </table>

        <div class="modal fade" id="review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                    class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">审核</h4>
                    </div>
                    <div class="modal-body">
                        <div class="progress" id="progress"></div>
                        <div id="old-review"></div>
                        <div id="old-remark"></div>
                        <input type="hidden" id="data-id">
                        <div class="form-group evaluation">
                            <label class="control-label" for="evaluation">审核意见</label>
                            <textarea class="form-control" id="evaluation" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="remark">评论/备注</label>
                            <textarea class="form-control" id="remark" rows="2"></textarea>
                        </div>
                        <div class="wechatNotice" data-type="{{\App\Notice::TYPE_REVIEW}}" data-way="{{\App\Notice::WAY_WECHAT}}"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="review-remark" class="btn btn-info" onclick="remark()">仅评论</button>
                        <button type="button" id="review-pass" class="btn btn-success" onclick="review(1)">通过</button>
                        <button type="button" id="review-refuse" class="btn btn-warning" onclick="review(-1)">拒绝
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/assets/js/datalist.js')}}"></script>
    <script>
        tableInit('{{$columns}}');
        $('#nav-datalist').addClass('active');
        removeIconClass();
    </script>
@endsection