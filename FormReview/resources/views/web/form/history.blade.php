@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>表单名称</th>
                <th>最后提交时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                <tr>
                    <th><a href="{{route('pub.info.show',$item->id)}}">{{$item->formname}}</a></th>
                    <th>{{$item->updated_at}}</th>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
@endsection
@section('js')
    <script>
    </script>
@endsection