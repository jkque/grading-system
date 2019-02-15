# Grading Sytem
> A Laravel-Vue Grading System.

## Features

- Laravel 5.7 
- Vue + VueRouter + Vuex + VueI18n + ESlint
- Pages with dynamic import and custom layouts
- Login, register and password reset
- Authentication with JWT
- Socialite integration
- Bootstrap 4 + Font Awesome 5

## Installation

- installed via git clone or download
-  `composer install` and `npm install` 
- `php artisan key:generate` and `php artisan jwt:secret`
- Edit `.env` and set your database connection details
- `php artisan migrate`
- `php artisan auth:generate-roles`
- `php artisan db:seed`


## Usage

#### Development

```bash
# build and watch
npm run watch

# serve with hot reloading
npm run hot
```

#### Production

```bash
npm run production
```

## Changelog

