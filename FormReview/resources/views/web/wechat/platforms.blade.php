@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="toolbar" class="btn-group">
            <button id="btn_add" type="button" class="btn btn-default">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>新增
            </button>
        </div>
        <table id="table" data-toolbar="#toolbar" data-show-pagination-switch="true"></table>
    </div>
@endsection
@section('js')
    <script src="{{cdnAsset('/node_modules/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js')}}"></script>
    <script src="{{cdnAsset('/node_modules/bootstrap-table/dist/extensions/editable/bootstrap-table-editable.min.js')}}"></script>
    <script src="{{cdnAsset('/assets/js/wechatplateforms.js')}}"></script>
    <script>
        tableInit();
        removeIconClass();
    </script>
@endsection