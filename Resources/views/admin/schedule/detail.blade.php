<div class="layui-card">
    <div class="layui-card-body">
@if (isset ($model) && isset ($model->logs) && sizeof ($model->logs) > 0)


        <table class="layui-table" lay-skin="line" lay-size="sm">

            <tbody>
            @foreach($model->logs as $log)
            <tr class="layui-bg-cyan">
                <td width="180">{{ $log->createdAtHuman }} 开始执行</td>
                <td>{{ (strcmp ('true', $log ['ua']) === 0) ? '失败' : '成功' }}</td>
            </tr>
                @foreach(explode ('; ', $log->data) as $line)
                <tr>
                    <td colspan="2"> - {{ $line }}</td>
                </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>

@else
    <p>没有更多数据...</p>

@endif

    </div>
</div>

