<?php

namespace App\Support\Status;

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

        return Device::firstOrCreate(['name' => $name]);
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
     * Setup default permissions for device service
     *
     * @param  \App\ServiceStatus
     * @return void
     */
    public function setDefaultPermissions(ServiceStatus $serviceStatus)
    {
        // Allow current user to update device service status
        $serviceStatus->users()->syncWithoutDetaching([$this->user->id]);

        // Allow "admin" users
        Role::admin()->users()->chunk(20, function($users) use ($serviceStatus) {
            $serviceStatus->users()->syncWithoutDetaching($users->pluck('id'));
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
     * Check if user can update device service status
     *
     * @param  \App\ServiceStatus  $serviceStatus
     * @return bool
     */
    public function canUpdateStatus(ServiceStatus $serviceStatus)
    {
        return $this->user
            ->serviceStatuses()
            ->where('id', $serviceStatus->id)
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
        $statusHasChanged = false;

        // Attempt to retrieve device service status
        $serviceStatus = $device->serviceStatuses()->where('service_id', $service->id)->first();

        if ($serviceStatus) {
            // Ensure user is allowed to update device service status
            if (! $this->canUpdateStatus($serviceStatus)) {
                $this->forbidden(__('app.unauthorized_service'));
            }

            // Check if existing device service status has changed
            if ($serviceStatus->status_id !== $status->id) {
                $serviceStatus->status_id = $status->id;
                $statusHasChanged = true;
            }

            // Update service status
            $serviceStatus->updated_by = $this->user->id;
            $serviceStatus->touch();
        } else {
            // Create device service status
            $serviceStatus = $device->serviceStatuses()->create([
                'service_id' => $service->id,
                'status_id' => $status->id,
                'updated_by' => $this->user->id,
            ]);

            $this->setDefaultPermissions($serviceStatus);

            $statusHasChanged = true;
        }

        if ($statusHasChanged) {
            StatusEvent::dispatch($serviceStatus);
        }

        return $serviceStatus;
    }
}
