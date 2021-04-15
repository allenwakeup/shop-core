<div class="layui-card layui-anim layui-anim-scale shadow"
     style="width: 98%;
     @isset ($details) cursor:pointer; @endisset">
@isset ($title)
    <div class="layui-card-header layui-bg-cyan"
         style="font-weight: bold;
        text-align: center;"> @if (isset ($quick_link)) <a href="{{ $quick_link }}" style="color: white;">{{ $title }}&nbsp;&gt;&gt;&gt;</a> @else {{ $title }} @endif </div>
@endisset
    <div class="layui-card-body"
         style="height: 280px;"
         @isset ($details)
         onclick="showWidgetDetail ('详细信息', '@if (! is_array ($details)) {{ $details }} @else {{ '<p>' . implode ('</p><p>', $details) . '</p>' }} @endif', '50%', '50%')"
         @endisset >
        @isset ($display)
        <div
            style="width: 100%;
            font-weight: bold;
            text-align: center;
            vertical-align: center;
            position: absolute;
            font-size: 48px;
            top: 40%;
            left: 0;">{{ $display }}</div>
        @endisset
        @isset ($tip)
        <div
            style="width: 100%;
            text-align: right;
            color: #8f8f8f;
            vertical-align: center;
            position: absolute;
            padding: 0;
            margin: 0;
            font-size: 12px;
            bottom: 0;
            left: 0;">{{ $tip }}&nbsp;&nbsp;</div>
        @endisset
    </div>
</div>
