# Inupress

A CMS on Laravel and Filament. Plugins are Composer packages.

> Pre-alpha (0.x): the API changes without notice.

## Install

```bash
composer create-project inupress/inupress my-site
cd my-site
php artisan inupress:install
php artisan serve
```

`create-project` creates `.env`, the application key and the SQLite database, and runs the migrations. `inupress:install` checks all of that again (it is safe to repeat) and creates the administrator; it asks for a name, email and password. Open <http://localhost:8000/admin>.

Without a terminal (scripts, CI): `php artisan inupress:install --email=you@example.com`. When `--password` is omitted a random one is printed once.

Requires PHP 8.3 or newer (tested on 8.4) with the usual Laravel extensions and `pdo_sqlite`, and Composer 2. Node is not needed.

## Plugins

```bash
composer require vendor/plugin     # install
composer remove vendor/plugin      # remove
php artisan inupress:plugin:list   # installed plugins
```

Composer is the only switch: there is no enable/disable toggle. Installed plugins show up in the admin panel on their own.

This starter includes [`inupress/pages`](https://github.com/inupress/pages). Pages are served at `/pages/{slug}`.

## Access

Only users with `is_root = true` can open the admin panel; create one with `php artisan inupress:install` or `php artisan inupress:make-root`. Roles and permissions do not exist yet.

## Learn more

Documentation, the plugin guide and the architecture decisions live in the framework repository: <https://github.com/inupress/framework>.

## License

[MIT](LICENSE)