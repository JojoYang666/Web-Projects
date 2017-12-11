@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>表单名称</th>
                <th>自定义样式代码</th>
                <th>最后提交时间</th>
                <th>审核</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                <tr id="item{{$item->id}}">
                    <th><a href="{{route('pub.info.create',$item->id)}}">{{$item->name}}</a></th>
                    <th><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#code{{$item->id}}">点击查看</button></th>
                    <!-- Modal -->
                    <div class="modal fade" id="code{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">{{$item->name}}自定义样式代码</h4>
                                </div>
                                <div class="modal-body">
                                    <pre>{{$item->customView}}</pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button data-id="{{$item->id}}" class="btn btn-success btn-review-pass">通过</button>
                                    <button data-id="{{$item->id}}" class="btn btn-warning btn-review-refuse">拒绝</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <th>{{$item->updated_at}}</th>
                    <th>
                        <button data-id="{{$item->id}}" class="btn btn-success btn-review-pass">通过</button>
                        <button data-id="{{$item->id}}" class="btn btn-warning btn-review-refuse">拒绝</button>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <div id="custom-refuse" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">拒绝</h4>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="refuse-reason" class="form-control" placeholder="请输入拒绝理由">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary modal-review-refuse">确认</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function () {
            $('.btn-review-pass').click(function () {
                var id=$(this).attr('data-id');
                $.post('/web/form/customReview',{
                    id:id,
                    result:1
                },function (data) {
                    if(data.status==0) {
                        toastr.success('审核成功！');
                        $('.modal').modal('hide');
                        $('#item' + id).remove();
                    }else {
                        toastr.error('审核失败！'+data.error);
                    }
                });
            });
            $('.btn-review-refuse').click(function () {
                var refuseModal=$('#custom-refuse');
                refuseModal.attr('data-id',$(this).attr('data-id'));
                refuseModal.modal('show');
            });
            $('.modal-review-refuse').click(function () {
                var refuseModal=$('#custom-refuse');
                var id=refuseModal.attr('data-id');
                $.post('/web/form/customReview',{
                    id:refuseModal.attr('data-id'),
                    result:2,
                    reason:$('#refuse-reason').val()
                },function (data) {
                    if(data.status==0) {
                        toastr.success('审核成功！');
                        $('.modal').modal('hide');
                        $('#item' + id).remove();
                    }else {
                        toastr.error('审核失败！'+data.error);
                    }
                });
            });
        });
    </script>
@endsection