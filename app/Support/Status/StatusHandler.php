<?php

namespace App\Support\Status;

use Illuminate\Support\Carbon;
use App\Device;
use App\Role;
use App\Service;
use App\ServiceStatus;
use App\Status;
use App\User;

class StatusHandler
{
    /**
     * @var \App\User
     */
    protected $user;

    /**
     * Create handler
     *
     * @param  \App\User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Throw a status exception
     *
     * @param  string  $message
     * @throws \App\Status\StatusException
     */
    public function error($message, $code = 400)
    {
        throw new StatusException($message, $code);
    }

    /**
     * Throw a status exception (403 Forbidden)
     *
     * @param  string  $message
     * @throws \App\Status\StatusException
     */
    public function forbidden($message, $code = 403)
    {
        $this->error($message, $code);
    }

    /**
     * Get device by name
     *
     * @param  string  $name
     * @return \App\Device
     */
    public function device($name)
    {
        $name = $this->normalizeDevice($name);
        $device = Device::where(['name' => $name])->first();

        // Create device if missing
        if (! $device) {
            $device  = Device::create(['name' => $name]);
            $this->setDefaultDeviceRelations($device);
        }

        return $device;
    }

    /**
     * Get service by name
     *
     * @param  string  $name
     * @return \App\Service
     */
    public function service($name)
    {
        $name = $this->normalizeService($name);

        return Service::firstOrCreate(['name' => $name]);
    }

    /**
     * Get (allowed) status by name
     *
     * @param  string  $name
     * @return \App\Status
     * @throws \App\Status\StatusException
     */
    public function status($name)
    {
        $name = $this->normalizeStatus($name);
        $status = Status::where(['name' => $name])->first();

        if (! $status) {
            $this->error(__('app.unknown_status'));
        }

        return $status;
    }

    /**
     * Normalize device name
     *
     * @param  string  $name
     * @return string
     */
    public function normalizeDevice($name)
    {
        return mb_strtolower($name);
    }

    /**
     * Normalize service name
     *
     * @param  string  $name
     * @return string
     */
    public function normalizeService($name)
    {
        return mb_strtolower($name);
    }

    /**
     * Normalize status name
     *
     * @param  string  $name
     * @return string
     */
    public function normalizeStatus($name)
    {
        return mb_strtoupper($name);
    }

    /**
     * Setup default users relations for newly created device
     *
     * @param  \App\Device
     * @return void
     */
    public function setDefaultDeviceRelations(Device $device)
    {
        // Attach user to device
        $device->users()->syncWithoutDetaching([$this->user->id]);

        // Attach "admin" users to device
        Role::admin()->users()->chunk(10, function($users) use ($device) {
            $device->users()->syncWithoutDetaching($users->pluck('id'));
        });
    }

    /**
     * Handle device service status
     *
     * @param  string  $deviceName
     * @param  string  $serviceName
     * @param  string  $statusName
     * @return \App\ServiceStatus
     * @throws \App\Status\StatusException
     */
    public function handleByNames($deviceName, $serviceName, $statusName)
    {
        return $this->handle(
            $this->device($deviceName),
            $this->service($serviceName),
            $this->status($statusName)
        );
    }

    /**
     * Check if user has device relation
     *
     * @param  \App\Device  $device
     * @return bool
     */
    public function userHasDevice(Device $device)
    {
        return $this->user
            ->devices()
            ->where('id', $device->id)
            ->exists();
    }

    /**
     * Handle device service status
     *
     * @param  \App\Device  $device
     * @param  \App\Service  $service
     * @param  \App\Status  $status
     * @return \App\ServiceStatus
     * @throws \App\Status\StatusException
     */
    public function handle(Device $device, Service $service, Status $status)
    {
        $now = Carbon::now();
        $statusHasChanged = false;

        // Ensure user is allowed to update device services
        if (! $this->userHasDevice($device)) {
            $this->forbidden(__('app.unauthorized_device'));
        }

        // Attempt to retrieve device service status
        $serviceStatus = $device->serviceStatuses()->where('service_id', $service->id)->first();

        if ($serviceStatus) {
            // Check if existing device service status has changed
            if ($serviceStatus->status_id !== $status->id) {
                $serviceStatus->status_id = $status->id;
                $statusHasChanged = true;
            }

            // Update "last check" date and user
            $serviceStatus->updated_by = $this->user->id;
            $serviceStatus->updated_at = $now;
            $serviceStatus->save();
        } else {
            // Create device service status
            $serviceStatus = $device->serviceStatuses()->create([
                'service_id' => $service->id,
                'status_id' => $status->id,
                'updated_by' => $this->user->id,
                'updated_at' => $now,
            ]);

            $statusHasChanged = true;
        }

        if ($statusHasChanged) {
            $this->serviceStatusEvent($serviceStatus);
        }

        return $serviceStatus;
    }

    /**
     * Get elapsed duration in seconds
     *
     * @param  \Illuminate\Support\Carbon  $date
     * @return int
     */
    public function elapsed(Carbon $date)
    {
        $elapsed = Carbon::now()->timestamp - $date->timestamp;

        return $elapsed > 0 ? $elapsed : 0;
    }

    /**
     * Handle device service status event
     *
     * @param  \App\ServiceStatus  $serviceStatus
     * @return \App\ServiceEvent
     */
    protected function serviceStatusEvent(ServiceStatus $serviceStatus)
    {
        // Attempt to retrieve latest service status event
        $latest = $serviceStatus->events()
            ->latest()
            ->first();

        if ($latest) {
            // Expects service status has changed
            $hasChanged = $latest->status_id !== $serviceStatus->status_id;

            // Set elapsed duration only if status has changed
            if ($hasChanged) {
                $latest->elapsed = $this->elapsed($latest->created_at);
            }

            $latest->updated_by = $this->user->id;
            $latest->save();

            // Return latest event if status has not changed
            if (! $hasChanged) {
                return $latest;
            }
        }

        // Create new device event
        $event = $serviceStatus->events()->create([
            'status_id' => $serviceStatus->status_id,
            'updated_by' => $this->user->id,
        ]);

        return $event;
    }
}
