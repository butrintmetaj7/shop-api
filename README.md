# Shop API

Laravel REST API for a shop application with product management and role-based access control.

## Tech Stack

- Laravel 12
- MySQL
- Laravel Sanctum (Authentication)
- PHP 8.2+

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan products:import
```

## Features

- Product import from Fake Store API
- Search and filter products
- User/Admin roles
- API versioning (v1)
- Paginated responses

## API Endpoints

Base URL: `http://localhost/api/v1`

### Authentication

**Register**
```
POST /api/v1/auth/register
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Login**
```
POST /api/v1/auth/login
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Logout** (requires auth)
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Profile** (requires auth)
```
GET /api/v1/auth/profile
Authorization: Bearer {token}
```

### Shop (Public)

**List Products**
```
GET /api/v1/shop/products
GET /api/v1/shop/products?search=laptop&category=electronics
```

Query params: `search`, `category`, `min_price`, `max_price`, `per_page`

**Get Product**
```
GET /api/v1/shop/products/{id}
```

### Admin (Requires Admin Role)

Admin routes require authentication + admin role. Regular users get 403 Forbidden.

**List Products**
```
GET /api/v1/admin/products
GET /api/v1/admin/products?search=laptop
Authorization: Bearer {token}
```

Admin responses include: `external_source`, `external_id`, `created_at`, `updated_at`

**Get Product**
```
GET /api/v1/admin/products/{id}
Authorization: Bearer {token}
```

**Update Product**
```
PUT /api/v1/admin/products/{id}
Authorization: Bearer {token}

{
    "title": "Updated Name",
    "price": 129.99,
    "description": "Updated description",
    "image": "https://..."
}
```

## Response Format

All responses follow this structure:

```json
{
    "success": true,
    "message": "Optional message",
    "data": { }
}
```

Paginated responses include: `current_page`, `last_page`, `per_page`, `total`, `from`, `to`

## Commands

```bash
# Import products
php artisan products:import

# Run tests
php artisan test
```

## Testing

```bash
php artisan test
```

## License

MIT
