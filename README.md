# Shop API

Shop application api built with Laravel.

## Features

- Import products from Fake Store API
- Update products via authenticated API endpoints
- Sync products without creating duplicates

## Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **External API**: Fake Store API
- **PHP**: 8.2+

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan products:import
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User authentication
- `POST /api/auth/logout` - User logout

### Products
- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get specific product
- `PUT /api/products/{id}` - Update product (authenticated)

## Commands

```bash
php artisan products:import  
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).