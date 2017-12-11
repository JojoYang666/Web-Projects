@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($forms as $form)
                <div class="col-sm-6 col-md-3">
                    <a href="{{ url('web/form/'.$form->id) }}">
                        <div class="thumbnail form-list-item">
                            @if($form->pic)
                                <img src="{{$form->pic}}" alt="{{$form->name}}">
                            @else
                                <img src="/storage/forms/default-pic.png" alt="{{$form->name}}">
                            @endif
                            <div class="caption">
                                <h5 class="title">{{$form->name}}</h5>
                                <p class="form-desc-in-list">{{$form->description}}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
