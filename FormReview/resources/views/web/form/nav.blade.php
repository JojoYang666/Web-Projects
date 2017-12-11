<div class="nav">
    {{$form->name}}
    <ul class="nav nav-tabs" role="tablist">
        <li id="nav-index" role="presentation"><a href="{{ route('web.form.show',$form->id) }}">概述</a></li>
        <li id="nav-edit" role="presentation"><a href="{{ route('web.form.edit',$form->id) }}">编辑</a></li>
        <li id="nav-setting" role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">设置 <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ route('web.admin.manageAdmins',$form->id) }}">管理员设置</a></li>
                <li><a href="{{ route('web.form.noticeSetting',$form->id) }}">通知设置</a></li>
            </ul>
        </li>
        <li id="nav-publish" role="presentation"><a href="{{ route('web.form.publish',$form->id) }}">发布</a></li>
        <li id="nav-datalist" role="presentation"><a href="{{ route('web.form.datalist',$form->id) }}">数据</a></li>
        <li id="nav-report" role="presentation"><a href="{{ route('web.form.report',$form->id) }}">报表</a></li>
        <li id="nav-custom" role="presentation"><a href="{{ route('web.form.custom',$form->id) }}">自定义样式</a></li>
    </ul>
</div>