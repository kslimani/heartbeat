# Heartbeat

__DISCLAIMER: This project is a work in progress.__

A simple "heartbeat" back-end application to keep track of services statuses.

## Configuration

See Laravel configuration [documentation](https://laravel.com/docs/5.8/configuration).

After configuration run database migrations, application installation and create new user with "admin" role :

```shell
php artisan migrate
php artisan app:install
php artisan app:create-admin-user --name <ADMIN_NAME> --email <ADMIN_EMAIL> --password <ADMIN_PASSWORD>
```

Configure the [task scheduler](https://laravel.com/docs/5.8/scheduling#introduction).

## Credits

This project is made with [Laravel](https://laravel.com/) PHP framework, [Bootstrap](https://getbootstrap.com/) library and [Material Design Icons](https://materialdesignicons.com/).

Heartbeat logo made by [Good Ware](https://www.flaticon.com/authors/good-ware) from [Flaticon](https://www.flaticon.com) is licensed by [CC 3.0 BY](https://creativecommons.org/licenses/by/3.0/).
