<?php

namespace App\Support\Status;

use App\Device;
use App\Role;
use App\Service;
use App\ServiceStatus;
use App\Status;
use App\User;
use Illuminate\Support\Carbon;

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

        if (! $device) {
            $device = Device::create([
                'name' => $name,
                'label' => $name,
            ]);
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
        $service = Service::where(['name' => $name])->first();

        if (! $service) {
            $service = Service::create([
                'name' => $name,
                'label' => $name,
            ]);
        }

        return $service;
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
     * Set default associations to service status
     *
     * @param  \App\ServiceStatus
     * @return void
     */
    public function setDefaultAssociations(ServiceStatus $serviceStatus)
    {
        $settings = [
            'is_updatable' => true,
            'is_mute' => false,
        ];

        // Attach current user
        $serviceStatus->users()->syncWithoutDetaching([
            $this->user->id => $settings,
        ]);

        // Attach "admin" users
        Role::byName(Role::ADMIN)
            ->users()
            ->chunk(20, function($users) use ($settings, $serviceStatus) {
                $associations = [];

                foreach ($users as $user) {
                    $associations[$user->id] = $settings;
                }

                $serviceStatus->users()->syncWithoutDetaching($associations);
            });

        $settings['is_updatable'] = false;

        // Attach "overseer" users
        Role::byName(Role::OVERSEER)
            ->users()
            ->chunk(20, function($users) use ($settings, $serviceStatus) {
                $associations = [];

                foreach ($users as $user) {
                    $associations[$user->id] = $settings;
                }

                $serviceStatus->users()->syncWithoutDetaching($associations);
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
        $first = $this->user
            ->serviceStatuses()
            ->where('id', $serviceStatus->id)
            ->first();

        return $first
            ? (bool) $first->pivot->is_updatable
            : false;
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
            // Typecast to integer because some db like sqlite return the value as string
            if ((int) $serviceStatus->status_id !== (int) $status->id) {
                $serviceStatus->status_id = $status->id;
                $serviceStatus->changed_at = Carbon::now();
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
                'changed_at' => Carbon::now(),
            ]);

            $this->setDefaultAssociations($serviceStatus);

            $statusHasChanged = true;
        }

        if ($statusHasChanged) {
            StatusEvent::dispatch($serviceStatus);
        }

        return $serviceStatus;
    }
}
