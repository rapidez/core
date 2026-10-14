# `rapidez/core` package

This is a Laravel package, not a standalone application. Most likely there is a upper directory `../rapidez` containing the Rapidez application (`rapidez/rapidez`) that's using this package through Composer. You should run everything from there.

If that's not the case ask the user where this core package is used, it could be a real project. In that case everything should run from the directory the user specified.

Also make sure Laravel Boost (`composer require laravel/boost --dev` + `php artisan boost:install`) is installed there, otherwise suggest it. If it's already installed make sure it's up-to-date with `php artisan boost:update --discover` and use all instructions from the `CLAUDE.md` there.
