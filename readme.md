![](https://github.com/kslimani/heartbeat/workflows/Integration%20tests/badge.svg) [![MIT license](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

# Heartbeat

A simple "heartbeat" back-end application to [keep track](docs/about.md#how-it-works) of services statuses.

## Getting started

See Laravel configuration [documentation](https://laravel.com/docs/6.x/configuration).

After configuration run database migrations, application installation and create new user with "admin" role :

```shell
php artisan migrate
php artisan hb:install
php artisan hb:create-admin-user --name <ADMIN_NAME> --email <ADMIN_EMAIL> --password <ADMIN_PASSWORD>
```

Then configure the Laravel [task scheduler](https://laravel.com/docs/6.x/scheduling#introduction).

Finally adjust `.env` file [configuration variables](docs/about.md#configuration) to your needs.

## Credits

This project is made with [Laravel](https://laravel.com/) PHP framework, [Bootstrap](https://getbootstrap.com/), [Bootstrap 4 Toggle](https://github.com/gitbrent/bootstrap4-toggle) libraries and [Material Design](https://materialdesignicons.com/) icons.

Heartbeat logo made by [Good Ware](https://www.flaticon.com/authors/good-ware) from [Flaticon](https://www.flaticon.com) is licensed by [CC 3.0 BY](https://creativecommons.org/licenses/by/3.0/).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
