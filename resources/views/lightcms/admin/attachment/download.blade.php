@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-card-body">
            @if(isset($message) && !empty($message))
                <div class="layui-text">{{ $message }}</div>
            @endif
        </div>
    </div>
@endsection

@section('js')

@endsection
