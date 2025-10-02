# Shop API

Shop application API built with Laravel.

## Features

- Import products from Fake Store API
- Update products via authenticated API endpoints
- Sync products without creating duplicates
- API versioning for backward compatibility
- Paginated product listings with search and filters
- Role-based access control (User/Admin)
- Database indexing for optimized search performance
- Different resource representations (Shop vs Admin)

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

## API Documentation

### Base URL
```
http://localhost/api/v1
```

### API Versioning
The API uses URL-based versioning. Current version: **v1**

All endpoints are prefixed with `/api/v1`

### Response Format
All API responses follow this structure:

**Success Response:**
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

**Paginated Response:**
```json
{
    "success": true,
    "data": [ ... ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
}
```

## API Endpoints

### Authentication

#### Register
```http
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "1|abc123...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

#### Logout (Requires Authentication)
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

#### Get Profile (Requires Authentication)
```http
GET /api/v1/auth/profile
Authorization: Bearer {token}
```

### Shop (Public - No Authentication Required)

#### List Products (Paginated)
```http
GET /api/v1/shop/products
GET /api/v1/shop/products?search=laptop
GET /api/v1/shop/products?category=electronics
GET /api/v1/shop/products?min_price=50&max_price=200
GET /api/v1/shop/products?search=phone&category=electronics&per_page=20
```

**Query Parameters:**
- `search` - Search in product title (indexed for performance)
- `category` - Filter by specific category (exact match)
- `min_price` - Minimum price filter
- `max_price` - Maximum price filter
- `per_page` - Results per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Product Name",
            "price": 109.95,
            "description": "Product description",
            "category": "men's clothing",
            "image": "https://..."
        }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
}
```

#### Get Single Product
```http
GET /api/v1/shop/products/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Product Name",
        "price": 109.95,
        "description": "Product description",
        "category": "men's clothing",
        "image": "https://..."
    }
}
```

### Admin (Requires Admin Role)

**Note:** Admin routes require both authentication AND admin role. Regular users will receive a 403 Forbidden response.

#### List Products (Paginated)
```http
GET /api/v1/admin/products
GET /api/v1/admin/products?search=laptop
GET /api/v1/admin/products?category=electronics
Authorization: Bearer {token}
```

**Query Parameters:** Same as shop route (search, category, min_price, max_price, per_page)

**Response:** Admin response includes additional fields (external_source, external_id, timestamps)

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Product Name",
            "price": 109.95,
            "description": "Product description",
            "category": "men's clothing",
            "image": "https://...",
            "rating": {"rate": 3.9, "count": 120},
            "external_source": "fakestore",
            "external_id": 1,
            "created_at": "2025-10-02T10:30:00.000000Z",
            "updated_at": "2025-10-02T10:30:00.000000Z"
        }
    ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
}
```

#### Get Single Product
```http
GET /api/v1/admin/products/{id}
Authorization: Bearer {token}
```

**Response:** Returns single product with admin fields (external_source, external_id, timestamps)

#### Update Product
```http
PUT /api/v1/admin/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Product Name",
    "price": 129.99,
    "description": "Updated description",
    "category": "electronics",
    "image": "https://..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "Product updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Product Name",
        "price": 129.99,
        "description": "Updated description",
        "category": "electronics",
        "image": "https://..."
    }
}
```

## Commands

```bash
# Import products from Fake Store API (default)
php artisan products:import

# Import from specific source
php artisan products:import --source=fakestore

# Run tests
php artisan test
```

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test file:
```bash
php artisan test tests/Feature/ProductControllerTest.php
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).