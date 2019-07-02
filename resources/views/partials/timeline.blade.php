    <div class="row">
        <ul class="timeline">
            @foreach ($events as $event)
            <li>
                <div class="timeline mdi mdi-24px {{ $event->toStatus->name === 'UP' ? 'mdi-check-circle text-success' : 'mdi-alert-circle text-danger' }}"></div>
                <div class="col">
                    <div class="timeline-date">{{ $event->label_date }}</div>
                    @unless (isset($withoutDeviceLine))
                    <div><a href="{{ route('service-events.show', ['id' => $event->serviceStatus->id]) }}">{{ $event->serviceStatus->service->label }}</a> @ {{ $event->serviceStatus->device->label }}</div>
                    @endunless
                    <div>
                        {{ __('app.status_from') }}<span class="badge">{{ $event->fromStatus->name }}</span>{{ __('app.status_to') }}<span class="badge">{{ $event->toStatus->name }}</span>
                    </div>
                    <span class="mdi mdi-timer-sand-empty"></span><span class="timeline-duration badge">{{ $event->label_duration }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
