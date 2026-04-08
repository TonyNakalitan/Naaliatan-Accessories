# Naaliatan's Accessories API Documentation

## Overview

This document describes the REST API endpoints available for Naaliatan's Accessories Management System. All API endpoints are protected by JWT (JSON Web Token) authentication, except for the login endpoint and API documentation.

## Base URL

```
http://localhost:8000/api
```

## Authentication

### Getting a JWT Token

To access protected API endpoints, you must first obtain a JWT token by authenticating with your email and password.

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "your.email@example.com",
  "password": "your_password"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Using the JWT Token

Include the JWT token in the `Authorization` header of all subsequent requests:

```
Authorization: Bearer <your_jwt_token>
```

## API Endpoints

### 1. Users

**Base Path:** `/api/users`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/users/{id}` | Get user by ID | Own user or Admin |
| GET | `/api/users` | Get all users | Admin only |
| POST | `/api/users` | Create new user | Admin only |
| PUT | `/api/users/{id}` | Update user | Own user or Admin |
| DELETE | `/api/users/{id}` | Delete user | Admin only |

**Example - Get Own Profile:**
```bash
curl -X GET "http://localhost:8000/api/users/1" \
  -H "Authorization: Bearer <token>"
```

### 2. Products

**Base Path:** `/api/products`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/products/{id}` | Get product by ID | Any authenticated user |
| GET | `/api/products` | Get all products | Any authenticated user |
| POST | `/api/products` | Create new product | Admin only |
| PUT | `/api/products/{id}` | Update product | Admin only |
| DELETE | `/api/products/{id}` | Delete product | Admin only |

**Example - Get All Products:**
```bash
curl -X GET "http://localhost:8000/api/products" \
  -H "Authorization: Bearer <token>"
```

### 3. Characters

**Base Path:** `/api/characters`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/characters/{id}` | Get character by ID | Any authenticated user |
| GET | `/api/characters` | Get all characters | Any authenticated user |
| POST | `/api/characters` | Create new character | Admin only |
| PUT | `/api/characters/{id}` | Update character | Admin only |
| DELETE | `/api/characters/{id}` | Delete character | Admin only |

### 4. Orders

**Base Path:** `/api/orders`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/orders/{id}` | Get order by ID | Own order or Admin |
| GET | `/api/orders` | Get all orders | Admin only |
| POST | `/api/orders` | Create new order | Any authenticated user |
| PUT | `/api/orders/{id}` | Update order | Admin only |
| DELETE | `/api/orders/{id}` | Delete order | Admin only |

**Example - Create Order:**
```bash
curl -X POST "http://localhost:8000/api/orders" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/ld+json" \
  -d '{
    "customer": "/api/users/1",
    "totalAmount": "1500.00",
    "status": "pending",
    "customerName": "John Doe",
    "customerAddress": "123 Main St",
    "city": "Manila",
    "province": "Metro Manila",
    "deliveryType": "standard",
    "phoneNumber": "09171234567"
  }'
```

### 5. Order Items

**Base Path:** `/api/order_items`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/order_items/{id}` | Get order item by ID | Own order's item or Admin |
| GET | `/api/order_items` | Get all order items | Admin only |
| POST | `/api/order_items` | Create new order item | Admin only |
| PUT | `/api/order_items/{id}` | Update order item | Admin only |
| DELETE | `/api/order_items/{id}` | Delete order item | Admin only |

### 6. Cart

**Base Path:** `/api/carts`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/carts/{id}` | Get cart by ID | Own cart or Admin |
| GET | `/api/carts` | Get all carts | Admin only |
| POST | `/api/carts` | Create new cart | Any authenticated user |
| PUT | `/api/carts/{id}` | Update cart | Own cart or Admin |
| DELETE | `/api/carts/{id}` | Delete cart | Admin only |

### 7. Cart Items

**Base Path:** `/api/cart_items`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/cart_items/{id}` | Get cart item by ID | Own cart's item or Admin |
| GET | `/api/cart_items` | Get all cart items | Admin only |
| POST | `/api/cart_items` | Add item to cart | Any authenticated user |
| PUT | `/api/cart_items/{id}` | Update cart item | Own cart's item or Admin |
| DELETE | `/api/cart_items/{id}` | Remove item from cart | Own cart's item or Admin |

**Example - Add Item to Cart:**
```bash
curl -X POST "http://localhost:8000/api/cart_items" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/ld+json" \
  -d '{
    "cart": "/api/carts/1",
    "product": "/api/products/1",
    "quantity": 2
  }'
```

### 8. Stock

**Base Path:** `/api/stocks`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/stocks/{id}` | Get stock by ID | Any authenticated user |
| GET | `/api/stocks` | Get all stock | Any authenticated user |
| POST | `/api/stocks` | Create stock entry | Admin only |
| PUT | `/api/stocks/{id}` | Update stock | Admin only |
| DELETE | `/api/stocks/{id}` | Delete stock | Admin only |

### 9. Stock Transactions

**Base Path:** `/api/stock_transactions`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/stock_transactions/{id}` | Get transaction by ID | Admin only |
| GET | `/api/stock_transactions` | Get all transactions | Admin only |
| POST | `/api/stock_transactions` | Create transaction | Admin only |
| PUT | `/api/stock_transactions/{id}` | Update transaction | Admin only |
| DELETE | `/api/stock_transactions/{id}` | Delete transaction | Admin only |

### 10. Activity Logs

**Base Path:** `/api/activity_logs`

| Method | Endpoint | Description | Permissions |
|--------|----------|-------------|-------------|
| GET | `/api/activity_logs/{id}` | Get log by ID | Admin only |
| GET | `/api/activity_logs` | Get all activity logs | Admin only |
| POST | `/api/activity_logs` | Create log entry | Admin only |
| PUT | `/api/activity_logs/{id}` | Update log | Admin only |
| DELETE | `/api/activity_logs/{id}` | Delete log | Admin only |

## API Documentation

### Swagger/OpenAPI Documentation

Interactive API documentation is available at:
```
http://localhost:8000/api/docs
```

### JSON-LD Context

JSON-LD context for each resource is available at:
```
http://localhost:8000/api/contexts/{resource_name}
```

## Error Responses

The API returns standard HTTP status codes:

- `200 OK` - Successful GET, PUT, or PATCH request
- `201 Created` - Successful resource creation
- `204 No Content` - Successful DELETE request
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Missing or invalid JWT token
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `500 Internal Server Error` - Server error

**Error Response Format:**
```json
{
  "@context": "/contexts/Error",
  "@type": "hydra:Error",
  "hydra:title": "An error occurred",
  "hydra:description": "Detailed error message"
}
```

## Pagination

Collection endpoints support pagination with the following query parameters:

- `page` - Page number (default: 1)
- `itemsPerPage` - Number of items per page (default: 30)

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products?page=2&itemsPerPage=10" \
  -H "Authorization: Bearer <token>"
```

## Filtering

Some endpoints support filtering. Check the specific endpoint documentation for available filters.

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products?stockQuantity=0" \
  -H "Authorization: Bearer <token>"
```

## Sorting

Collections can be sorted using the `order` parameter:

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products?order[price]=desc" \
  -H "Authorization: Bearer <token>"
```

## Rate Limiting

API rate limiting may be applied to prevent abuse. If you exceed the rate limit, you'll receive a `429 Too Many Requests` response.

## CORS

The API supports Cross-Origin Resource Sharing (CORS) for development environments. Production CORS settings should be configured appropriately.

## Version

Current API Version: 1.0.0

## Support

For API support or questions, please contact the development team.