<html>
<h1>自定义样式审核通知</h1>
@if($result==1)
    <p>恭喜你的表单【<a href="{{route('web.form.show',$form->id)}}">{{$form->name}}</a>】自定义样式通过审核！</p>
@else
    <p>很遗憾你的表单【<a href="{{route('web.form.show',$form->id)}}">{{$form->name}}</a>】自定义样式没有通过审核，原因：{{$reason}}</p>
@endif
</html>