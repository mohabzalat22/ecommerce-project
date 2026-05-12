# PHP Minimal Framework

Mo Framework is a compact PHP 8.3 learning project that demonstrates the core pieces of a custom framework: a front controller, a router, a service container, config bootstrap, Eloquent database setup, and a Symfony Console command for migrations.

## Highlights

- Custom HTTP entry point in `public/index.php`
- Lightweight router and request handling in `Framework/`
- Application controllers in `app/Http/Controllers/`
- API routes registered in `routes/api.php`
- Database bootstrap in `bootstrap/database.php`
- CLI support through `artisan`

## Requirements

- PHP 8.3 or newer
- Composer
- A writable `storage/` directory
- A database driver that matches your `DB_CONNECTION` value

## Quick Start

Install dependencies.

```bash
composer install
```

Create your environment file from the provided example.

```bash
cp .env.example .env
```

If you use SQLite, make sure the database file exists.

```bash
touch storage/database.sqlite
```

## Run The App

Start the built-in PHP server from the project root:

```bash
php -S localhost:8000 -t public proxy.php
```

Open <http://localhost:8000> in your browser.

`bootstrap/database.php` loads the connection from `config/database.php` and boots Eloquent.

Run the migration command with:

```bash
./artisan migrate
```

If your shell does not allow direct execution, use:

```bash
php artisan migrate
```

Migration files live in `database/migrations/`. The migration runner accepts files that return either:

- an array with an `up` callable
- an object with an `up()` method

## Tests

There is a simple standalone request test runner at `tests/run_home_api_test.php`.

```bash
php tests/run_home_api_test.php
```

It boots the framework and checks the default home API endpoints.

## Project Structure

- `public/index.php` — HTTP entry point
- `bootstrap/app.php` — loads env, config, bindings, and routes
- `bootstrap/database.php` — boots the database layer
- `Framework/` — kernel, router, request, and command support
- `app/Http/Controllers/` — application controllers
- `app/Providers/ConfigServiceProvider.php` — config registration
- `config/` — app and database configuration
- `database/migrations/` — migration files
- `routes/api.php` — route definitions
- `tests/` — lightweight manual test script

## Development Commands

The Composer scripts in `composer.json` are:

```bash
composer lint
composer fix
```

Use `composer lint` to check formatting and `composer fix` to apply fixes with PHP CS Fixer.

## Notes

- The bootstrap layer defines `env()` before config files are loaded.
- The service container is created in `public/index.php` and reused during bootstrap.
- Composer autoloading maps `Framework\` to `Framework/` and `App\` to `app/`.

## License

MIT
