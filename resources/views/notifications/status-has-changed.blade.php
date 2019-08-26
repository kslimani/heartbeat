@component('mail::message')

# @lang('app.services_statuses')

@component('mail::report-statuses', ['statuses' => $statuses])
@endcomponent

# {{ trans_choice('app.new_events', $changes->count(), ['value' => $changes->count()]) }}

{{-- Service events table --}}
@component('mail::table')
| @lang('app.date') | @lang('app.status_changed') | @lang('app.from')  | @lang('app.to') |
|:------------------|:---------------------------:|:------------------:|:---------------:|
@foreach ($changes as $change)
| {{ $change->date }} | [{{ $change->service->name }}]({{ route('service-statuses.show', ['id' => $change->id]) }}) @ {{ $change->device->name }} | {{ $change->from->name }} | @component('mail::text', ['color' => $change->to->name === 'UP' ? 'success' : 'error']) {{ $change->to->name }} @endcomponent |
@endforeach
@endcomponent

{{-- Button --}}
@component('mail::button', ['url' => route('home')])
@lang('app.services_statuses')
@endcomponent

{{-- Report date --}}
@component('mail::report-date')
@lang('app.generated_on') {{ $date }}
@endcomponent

@endcomponent
