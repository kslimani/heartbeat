<?php

namespace Tests\Unit\Support\Status\Report\Latest;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusHasChanged;
use App\Role;
use App\User;
use App\ServiceEvent;
use App\Support\Status\StatusHandler;
use App\Support\Status\StatusException;
use App\Support\Status\Report\Latest\Reporter;

class ReporterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupApp();
    }

    public function testItReport()
    {
        $admin = User::where('email', $this->adminEmail)->first();
        $overseer = factory(User::class)->create();
        $overseer->roles()->syncWithoutDetaching([Role::byName(Role::OVERSEER)->id]);
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $handler1 = new StatusHandler($user1);
        $handler2 = new StatusHandler($user2);

        // Set report tolerance delay to 3 minutes
        config(['app.report_tolerance_delay' => 180]);

        Notification::fake();

        // 13h00 : update device service statuses (all services UP)
        $fakeNow = Carbon::create(2019, 5, 21, 13, 0, 0);
        Carbon::setTestNow($fakeNow);
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'UP');

        // No user notified and events are not handled yet
        Notification::assertNothingSent();
        $this->assertDatabaseHas('service_events', [
            'is_handled' => false,
        ]);

        // 13h01 : run report
        Carbon::setTestNow($fakeNow->addMinutes(1));
        $notified = Reporter::report();

        // No user notified yet because only one minute elapsed since status change
        $this->assertEquals(0, $notified);
        Notification::assertNothingSent();

        // 13h03 : run report
        Carbon::setTestNow($fakeNow->addMinutes(2));
        $notified = Reporter::report();

        // All users notified
        $this->assertEquals(4, $notified);
        Notification::assertSentTo([$admin, $overseer, $user1, $user2], StatusHasChanged::class, function ($notification) {
            return $notification->report->changes()->count() === 3;
        });

        // All events handled
        $this->assertDatabaseMissing('service_events', [
            'is_handled' => false,
        ]);

        Notification::fake(); // Clear notification queue

        // 13h04 : one service is DOWN
        Carbon::setTestNow($fakeNow->addMinutes(1));
        $handler1->handleByNames('device001', 'service001', 'DOWN');

        // Event not handled yet
        $this->assertDatabaseHas('service_events', [
            'is_handled' => false,
        ]);

        // 13h05 : run report
        Carbon::setTestNow($fakeNow->addMinute());
        $notified = Reporter::report();

        // No user notified and event is not handled yet
        $this->assertEquals(0, $notified);
        Notification::assertNothingSent();
        $this->assertDatabaseHas('service_events', [
            'is_handled' => false,
        ]);

        // 13h06 : update device service statuses (all services UP again)
        Carbon::setTestNow($fakeNow->addMinutes(1));
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'UP');

        Notification::assertNothingSent();

        // 13h07 : run report
        Carbon::setTestNow($fakeNow->addMinutes(1));
        $notified = Reporter::report();

        // No user notified (service back to UP before tolerance delay)
        $this->assertEquals(0, $notified);
        Notification::assertNothingSent();
        // Event is handled
        $this->assertDatabaseMissing('service_events', [
            'is_handled' => false,
        ]);

        // 13h08 : update device service statuses (one service DOWN)
        Carbon::setTestNow($fakeNow->addMinutes(1));
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'DOWN');

        Notification::assertNothingSent();
        $this->assertDatabaseHas('service_events', [
            'is_handled' => false,
        ]);

        // 13h11 : run report
        Carbon::setTestNow($fakeNow->addMinutes(3));
        $notified = Reporter::report();

        // Admin, Overseer and user2 notified
        $this->assertEquals(3, $notified);
        Notification::assertSentTo([$admin, $overseer, $user2], StatusHasChanged::class, function ($notification) {
            return $notification->report->changes()->count() === 1;
        });

        // User1 not notified (no changes for device001)
        Notification::assertNotSentTo($user1, StatusHasChanged::class);

        // All events handled
        $this->assertDatabaseMissing('service_events', [
            'is_handled' => false,
        ]);
    }
}
