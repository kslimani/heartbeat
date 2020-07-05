<?php

namespace Tests\Unit\Support\Status;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use App\Status;
use App\User;
use App\Support\Status\InactiveHandler;
use App\Support\Status\StatusHandler;

class InactiveHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupApp();
    }

    public function testItHandle()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $device = $handler->device('device001');
        $service = $handler->service('service001');
        $up = $handler->status('UP');
        $inactive = Status::inactive();

        // 13h00 : update device service status (UP)
        $fakeNow = Carbon::create(2019, 5, 21, 13, 0, 0);
        Carbon::setTestNow($fakeNow);
        $serviceStatus = $handler->handle($device, $service, $up);

        $this->assertDatabaseHas('service_statuses', [
            'device_id' => $device->id,
            'service_id' => $service->id,
            'status_id' => $up->id,
        ]);

        InactiveHandler::handle();

        // Service still active
        $this->assertDatabaseHas('service_statuses', [
            'device_id' => $device->id,
            'service_id' => $service->id,
            'status_id' => $up->id,
        ]);

        // Mock inactivity period
        $duration = config('app.status_inactive_duration');
        Carbon::setTestNow($fakeNow->addSeconds($duration));
        InactiveHandler::handle();

        // Service status set to "inactive"
        $this->assertDatabaseHas('service_statuses', [
            'device_id' => $device->id,
            'service_id' => $service->id,
            'status_id' => $inactive->id,
            'changed_at' => $fakeNow->toDateTimeString(),
        ]);

        // Event added to service history
        $this->assertDatabaseHas('service_events', [
            'service_status_id' => $serviceStatus->id,
            'from_status_id' => $up->id,
            'to_status_id' => $inactive->id,
            'elapsed' => null,
            'is_handled' => false,
        ]);
    }
}
