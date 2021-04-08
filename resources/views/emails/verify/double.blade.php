@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            @lang('pages.app.name')
        @endcomponent
    @endslot
    您好，@if (!empty ($title)) 关于「{{ $title }}」的结果如下  @else 数据检查情况如下 @endif

@component('mail::panel')
@if(! empty ($run_at)) 邮件生成时间：{{ $run_at }} @endif
@if(! empty ($created_at)) 数据采集时间：{{ $created_at }} @endif


@if(! empty ($description))
@if(is_array ($description))
@foreach ($description as $str)
    {{ $str }}
@endforeach
@else {{ $description }}
@endif
@endif
@endcomponent


@if (! empty ($count) && is_array ($count))
### 数据量比较

| 来源 | 目标 | 差异 |
| :---: | :---: | :---:|
| {{ array_get ($count, 'source', 0) }} | {{ array_get ($count, 'target', 0) }} | {{ array_get ($count, 'diff', 0) }} |
@else

    没有差异
@endif

@if (isset ($details) && ! empty ($details) && is_array ($details) && sizeof ($details) > 0)
### 差异详情

@foreach($details as $row_num => $item)
@if ($row_num < 200)


@foreach($item as $key => $val) | {{ $key }} @endforeach |
@foreach($item as $key => $val) | :---: @endforeach |
@foreach($item as $key => $val) | {{ $val }} @endforeach |


@else
    @break
@endif
@endforeach

@if (sizeof ($details) > 200)

    还有{{ sizeof ($details) - 200 }}条没有显示...请参考附件内容
@endif

@else
    没有更多的差异详情
@endif


@if (isset ($details_table_only) && ! empty ($details_table_only) && is_array ($details_table_only) && sizeof ($details_table_only) > 0)
### 数据表格

@foreach($details_table_only [0] as $key => $val) | {{ $key }} @endforeach |
@foreach($details_table_only [0] as $key => $val) | :---: @endforeach |
@foreach($details_table_only as $row_num => $item)
@if ($row_num < 200)
@foreach($item as $key => $val) | {{ $val }} @endforeach |
@else
@break
@endif
@endforeach

@if (sizeof ($details_table_only) > 200)

    还有{{ sizeof ($details_table_only) - 200 }}条没有显示...请参考附件内容
@endif

@else
    没有更多数据
@endif


    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} @lang('pages.app.name'). All rights reserved.
        @endcomponent
    @endslot
@endcomponent