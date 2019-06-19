<?php

namespace Tests\Unit\Support\Status;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use App\User;
use App\Status;
use App\Support\Status\StatusHandler;
use App\Support\Status\StatusException;

class StatusHandlerTest extends TestCase
{
    use RefreshDatabase;

    public $adminEmail = 'admin@test.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->installApplication($this->adminEmail, 'secret123');
    }

    public function testItThrowStatusException()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);

        $this->expectException(StatusException::class);

        $handler->error('bigbadaboom!');
    }

    public function testItNormalizeDevice()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $normalized = $handler->normalizeDevice('My.DEVICE-Name');

        $this->assertSame('my.device-name', $normalized);
    }

    public function testItNormalizeService()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $normalized = $handler->normalizeService('Foo-BaR');

        $this->assertSame('foo-bar', $normalized);
    }

    public function testItNormalizeStatus()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $normalized = $handler->normalizeStatus('My_Status');

        $this->assertSame('MY_STATUS', $normalized);
    }

    public function testItGetDevice()
    {
        $admin = User::where('email', $this->adminEmail)->first();
        $adminUser = $this->createAdminUser();
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $name = 'my.device';

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($user->isAdmin());

        $this->assertDatabaseMissing('devices', [
            'name' => $name,
        ]);

        $this->assertDatabaseMissing('device_user', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('device_user', [
            'user_id' => $admin->id,
        ]);

        $this->assertDatabaseMissing('device_user', [
            'user_id' => $adminUser->id,
        ]);

        $device = $handler->device($name);

        $this->assertInstanceOf(\App\Device::class, $device);

        // Device has been created
        $this->assertDatabaseHas('devices', [
            'name' => $name,
        ]);

        // Authorized user has been granted to device
        $this->assertDatabaseHas('device_user', [
            'user_id' => $user->id,
            'device_id' => $device->id,
        ]);

        // Admin users has been granted to device
        $this->assertDatabaseHas('device_user', [
            'user_id' => $admin->id,
            'device_id' => $device->id,
        ]);

        $this->assertDatabaseHas('device_user', [
            'user_id' => $adminUser->id,
            'device_id' => $device->id,
        ]);

        $again = $handler->device($name);

        $this->assertSame($device->id, $again->id);
    }

    public function testItGetService()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $name = 'my-service';

        $this->assertDatabaseMissing('services', [
            'name' => $name,
        ]);

        $service = $handler->service($name);

        $this->assertInstanceOf(\App\Service::class, $service);

        $this->assertDatabaseHas('services', [
            'name' => $name,
        ]);

        $again = $handler->service($name);

        $this->assertSame($service->id, $again->id);
    }

    public function testItGetStatus()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);

        $status = $handler->status('up');

        $this->assertInstanceOf(\App\Status::class, $status);
        $this->assertSame('UP', $status->name);

        $again = $handler->status('Up');

        $this->assertSame($status->id, $again->id);
        $this->assertSame($status->name, $again->name);

        $this->expectException(StatusException::class);

        $handler->status('unknown');
    }

    public function testItHandle()
    {
        $user = factory(User::class)->create();
        $handler = new StatusHandler($user);
        $device = $handler->device('my.device');
        $service = $handler->service('acme-srv');
        $status = $handler->status('UP');
        $inactive = Status::inactive();

        $fakeNow = Carbon::create(2019, 5, 21);
        Carbon::setTestNow($fakeNow);

        $this->assertDatabaseMissing('service_statuses', [
            'device_id' => $device->id,
            'service_id' => $service->id,
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseMissing('service_events', [
            'to_status_id' => $status->id,
        ]);

        $serviceStatus = $handler->handle($device, $service, $status);

        $this->assertInstanceOf(\App\ServiceStatus::class, $serviceStatus);
        $this->assertSame($user->id, $serviceStatus->updated_by);
        $this->assertTrue($fakeNow->eq($serviceStatus->updated_at));

        $this->assertDatabaseHas('service_statuses', [
            'id' => $serviceStatus->id,
            'device_id' => $device->id,
            'service_id' => $service->id,
            'status_id' => $status->id,
        ]);

        $this->assertDatabaseHas('service_events', [
            'service_status_id' => $serviceStatus->id,
            'from_status_id' => $inactive->id,
            'to_status_id' => $status->id,
            'elapsed' => null,
            'updated_at' => $fakeNow->toDateTimeString(),
        ]);

        $fakeNow = Carbon::create(2019, 5, 22);
        Carbon::setTestNow($fakeNow);

        $again = $handler->handle($device, $service, $status);

        $this->assertInstanceOf(\App\ServiceStatus::class, $again);
        $this->assertSame($user->id, $again->updated_by);
        $this->assertTrue($fakeNow->eq($again->updated_at));
        $this->assertSame($serviceStatus->id, $again->id);

        $this->assertDatabaseHas('service_events', [
            'service_status_id' => $again->id,
            'to_status_id' => $status->id,
            'elapsed' => null, // Not updated because status has not changed
        ]);

        $fakeNow = Carbon::create(2019, 5, 23);
        Carbon::setTestNow($fakeNow);

        $down = $handler->status('DOWN');
        $finally = $handler->handle($device, $service, $down);

        $this->assertInstanceOf(\App\ServiceStatus::class, $finally);
        $this->assertSame($user->id, $finally->updated_by);
        $this->assertTrue($fakeNow->eq($finally->updated_at));
        $this->assertSame($serviceStatus->id, $finally->id);
        $this->assertSame($down->id, $finally->status_id);

        // Device service status was "UP" during two days
        $twoDaysInSeconds = 172800;

        // Latest event elapsed duration has been updated
        $this->assertDatabaseHas('service_events', [
            'service_status_id' => $again->id,
            'from_status_id' => $inactive->id,
            'to_status_id' => $status->id,
            'elapsed' => $twoDaysInSeconds,
            'updated_by' => $user->id,
        ]);

        // New device event created with "down" status
        $this->assertDatabaseHas('service_events', [
            'service_status_id' => $finally->id,
            'from_status_id' => $status->id,
            'to_status_id' => $down->id,
            'elapsed' => null,
        ]);

        Carbon::setTestNow(); // Clear mock

        // It throw a status exception if user has no device relation
        $unauthorizedUser = factory(User::class)->create();
        $handler = new StatusHandler($unauthorizedUser);

        $this->expectException(StatusException::class);
        $this->expectExceptionCode(403);

        $handler->handle($device, $service, $status);
    }
}
