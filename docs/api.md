# Service status Api


## Check Api key

Check if status api key is valid.

`POST /api/status/check`

__Data params :__

```json
{
    "key": "XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX"
}
```

__Success response :__

Code : 200

Content : `{}`

__Error response :__

Code : 403

Content : `{"error":"Forbidden"}`

__Example :__

```shell
curl -v -X POST -H "Content-Type: application/json" --data '{"XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX"}' "https://hb.example.com/api/status/check"
```


## Device service status

Create, refresh or update a device service status.

`POST /api/status`

__Data params :__

```json
{
    "key": "XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX",
    "device": "DEVICE-IDENTIFIER",
    "service": "SERVICE-IDENTIFIER",
    "status": "SERVICE-STATUS"
}
```

Allowed characters for __"device"__ and __"service"__ are alphanumeric, dot, dash and underscore. _(case insensitive)_

Allowed characters for __"status"__ are only letters and underscore. _(case insensitive)_

Default _known_ status accepted values are `UP`, `DOWN` and `INACTIVE`.

__Success response :__

Code : 200

Content : `{}`

__Error responses :__

Code : 400

Content : `{"error":"Unknown status"}`   _(the error message may differ)_

Code : 422

Content : `{"error":"Invalid Data"}`

Code : 403

Content : `{"error":"Forbidden"}`

__Example :__

```shell
curl -v -X POST -H "Content-Type: application/json" --data '{"XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX"}, "device":"my-host", "service":"my-service", "status":"up"}' "https://hb.example.com/api/status"
```
