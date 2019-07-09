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
use App\Support\AppStore;

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

        Notification::fake();

        // 13h00 : mock latest service event
        $fakeNow = Carbon::create(2019, 5, 21, 13, 0, 0);
        Carbon::setTestNow($fakeNow);
        AppStore::put(ServiceEvent::LATEST, $fakeNow);

        // 13h01 : update device service statuses (all services UP)
        Carbon::setTestNow($fakeNow->addMinute());
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'UP');

        Notification::assertNothingSent();

        $report = Reporter::report();

        // All users notified
        Notification::assertSentTo([$admin, $overseer, $user1, $user2], StatusHasChanged::class, function ($notification) {
            return $notification->report->changes()->count() === 3;
        });

        // 13h02 and 30 seconds later : one service is DOWN
        Carbon::setTestNow($fakeNow->addSeconds(30));
        $handler1->handleByNames('device001', 'service001', 'DOWN');

        // 13h02 : update device service statuses (all services UP again)
        Carbon::setTestNow($fakeNow->addMinute());
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'UP');

        // Clear notification queue
        Notification::fake();
        Notification::assertNothingSent();

        $report = Reporter::report();

        // No user notified (service back to UP before reporting)
        Notification::assertNothingSent();

        // 13h03 : update device service statuses (one service DOWN)
        Carbon::setTestNow($fakeNow->addMinute());
        $handler1->handleByNames('device001', 'service001', 'UP');
        $handler1->handleByNames('device001', 'service002', 'UP');
        $handler2->handleByNames('device002', 'service001', 'DOWN');

        $report = Reporter::report();

        // Admin, Overseer and user2 notified
        Notification::assertSentTo([$admin, $overseer, $user2], StatusHasChanged::class, function ($notification) {
            return $notification->report->changes()->count() === 1;
        });

        // User1 not notified (no changes for device001)
        Notification::assertNotSentTo($user1, StatusHasChanged::class);

        Carbon::setTestNow(); // Clear mock
    }
}
