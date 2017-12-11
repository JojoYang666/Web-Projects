<html>
    <h1>管理员邀请</h1>
    <p>{{Auth::user()->name}}邀请您成为<a href="{{route('web.form.show',$form->id)}}">{{$form->name}}</a>的管理员。</p>
    <a href="{{route('web.message')}}">点击查看详情</a>
</html>