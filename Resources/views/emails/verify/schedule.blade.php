@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            @lang('pages.app.name')
        @endcomponent
    @endslot
    您好，队列任务状态检查情况如下

@component('mail::panel')
@if(!empty ($run_at)) 邮件生成时间：{{ $run_at }} @endif

@isset ($logs_status)
@foreach($logs_status as $name => $status)
@if(isset ($status) && ! empty ($status) && is_array ($status))
@if(array_get ($status, 'true', 0) > 0)
* {{ $name }} 失败了 {{ array_get ($status, 'true', 0) }} 次！
@elseif(array_get ($status, 'true', 0) === 0 && array_get ($status, 'false', 0) === 0)
* {{ $name }} 一次也没执行！
@endif
@endif
@endforeach
@endisset
@endcomponent

## 任务列表

@if(! is_null ($schedules) && sizeof ($schedules) > 0)
@foreach($schedules as $index => $schedule)
### {{ $index + 1 }}. {{ $schedule->name }}（{{ $schedule->cron }}）

{{ $schedule->description }}
@if(! is_null ($schedule->logs) && sizeof ($schedule->logs) > 0)
@component('mail::panel')
@foreach($schedule->logs as $l_idx => $log)
@if ($l_idx === 0)
* {{ $log->created_at }} @if ($log->ua === 'true') 「「 执行失败 」」 @else 执行成功 @endif

@foreach(explode ('; ', $log->data) as $line)
    - {{ $line }}
@endforeach
@endif
@endforeach
@endcomponent
@endif
@endforeach
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