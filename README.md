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

This starter includes [`inupress/pages`](https://github.com/inupress/pages),
[`inupress/menus`](https://github.com/inupress/menus),
[`inupress/widgets`](https://github.com/inupress/widgets),
[`inupress/seo`](https://github.com/inupress/seo) and
[`inupress/theme-default`](https://github.com/inupress/theme-default). Pages are served at
`/pages/{slug}`, the navigation and the footer are editable under **Menus** and **Widgets**,
`/sitemap.xml` and `/robots.txt` are served, and there is nothing to build: no Node, no bundler.

## Navigation and blocks

A theme decides where a plugin may put something, and nothing breaks when a plugin is missing:

```blade
@includeIf('inupress-menus::nav', ['name' => 'primary'])
@includeIf('inupress-widgets::area', ['name' => 'footer'])
```

Menus are built out of pages and custom links, widgets fill the areas your theme renders
(`footer`, `sidebar`, `home` in the default theme).

## Themes

The site renders through `inupress/theme-default` out of the box — a Tailwind 4 shell with Alpine.
Nothing to configure and nothing to build: a theme is a Composer package holding views, a `theme.json`
manifest and its **prebuilt** CSS/JS, so you never run Node. If something is off with the assets:

```bash
php artisan inupress:theme:publish    # re-copy them into public/vendor (done on the first request too)
```

To change the look without editing the package, create a child theme in your own project:

```
themes/acme/theme.json     {"name": "Acme", "parent": "inupress/theme-default"}
themes/acme/resources/views/components/layout.blade.php
```

then set `INUPRESS_THEME=acme`. The child overrides the parent's views one by one and survives
`composer update`. See <https://github.com/inupress/framework/blob/main/docs/themes.md>.

## Access

Only users with `is_root = true` can open the admin panel; create one with `php artisan inupress:install` or `php artisan inupress:make-root`. Roles and permissions do not exist yet.

## Learn more

Documentation, the plugin guide and the architecture decisions live in the framework repository: <https://github.com/inupress/framework>.

## License

[MIT](LICENSE)