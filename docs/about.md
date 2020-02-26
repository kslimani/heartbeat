## Introduction

Hearbeat is a simple application to keep track of devices services statuses.

Service statuses are created, refreshed or updated with a very simple [web API](api.md).

Heartbeat target very small projects and __is not__ intended to handle a large number of services.


## How it works

Heartbeat is completely agnostic of "what" or "where" service is. It act as a "listener", and therefore, does not "probe" any service.

Heartbeat role is to check every minute if any service status has changed and eventually notify associated users.

External HTTP client role is to [refresh service status](api.md#device-service-status) at regular intervals. _(client implementation is not part of this project)_

The default _known_ statuses are "UP", "DOWN" and "INACTIVE". (default is "INACTIVE")

When Heartbeat detect that a service status has changed, it does not notify associated users until elapsed time since change is greater than "report tolerance delay".

If a service status is set back to previous status before "report tolerance delay" is elapsed, then no notification are send to associated users.

If a service status in not refreshed since a "status inactive duration" then status is changed to "INACTIVE".


## Configuration

Available environment variables are :

_(API)_

* `API_THROTTLE` : API throttle middleware (default is "60,1")
* `REPORT_TOLERANCE_DELAY` : minimum delay in seconds before service status change is reported (default is 60)
* `STATUS_INACTIVE` : minimum delay in seconds before service status is considered inactive (default is 120)

_(APP)_

* `MAX_PAST_EVENTS` : maximum past events displayed on services statuses page (default is 10)
* `PAGE_REFRESH_INTERVAL` : page refresh interval in seconds (default is 60)
* `PAGINATE_LIMIT` : pagination limit (default is 10)
* `SEARCH_LIMIT` : search result limit (default is 15)


## User roles

Default available roles are "admin" and "overseer". (newly created user have no role)

A service status is automatically created when updated for the first time by the client.

This newly created service status is automatically associated to :

* "key owner" user (can change status and be notified)
* all users with "admin" role (can change status and be notified)
* all users with "overseer" role (can only be notified)

Users with "admin" role can manage device service permissions.


## Clients

* [heartbeat-client-bash](https://github.com/kslimani/heartbeat-client-bash) - Simple Heartbeat bash client using curl
