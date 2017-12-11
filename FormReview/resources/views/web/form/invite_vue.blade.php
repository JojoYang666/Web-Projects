@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">邀请管理员</div>
                    <div class="panel-body">
                        <div id="app"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{cdnAsset('/assets/js/invite.js')}}"></script>
@endsection
