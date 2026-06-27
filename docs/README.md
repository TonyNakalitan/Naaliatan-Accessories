# Naaliatan Accessories — Project Documentation

A Symfony 7.4 full-stack e-commerce platform for game-character-themed accessories. Features a REST API with JWT authentication, a staff/admin dashboard, product and character management, shopping cart, order management, and payment processing.

---

## Table of Contents

1. [Changelog](#changelog)
2. [Tech Stack](#tech-stack)
3. [Installation & Setup](#installation--setup)
4. [Environment Variables](#environment-variables)
5. [Authentication](#authentication)
6. [API Reference](#api-reference)
   - [Auth](#auth)
   - [Profile](#profile)
   - [Characters](#characters)
   - [Products](#products)
   - [Cart](#cart)
   - [Orders](#orders)
   - [Payments](#payments)
7. [Roles & Permissions](#roles--permissions)
8. [Error Responses](#error-responses)

---

## Changelog

### v2.1.0 — June 2026

#### 🆕 New Features

- **Unified table layout across all management pages** — Payment Management, Items Management, Shopping Cart, Character Management, and Activity Logs pages all now use the same clean table + hero detail panel design as User Management. Clicking any row or the eye button opens a full-screen hero panel with a gradient background, circular image/icon frame, and slide-in detail sections.
- **Character Management — Color column** — The character table now shows a live color swatch alongside the hex code (e.g. `#ff6b6b`) for each character's brand color, making it easy to identify characters at a glance.
- **Character Management — View button** — The View button in the table and hero panel now correctly navigates to the character's detail page (`/admin/characters/{id}/show` or `/staff/characters/{id}/show`) instead of the customer-facing route. The Back and Edit buttons on the show page are also now role-aware (admin vs. staff).
- **Activity Logs — Combined search + action filter** — Search and the action-type dropdown now filter simultaneously in real time without a page reload.
- **Shopping Cart — Color-coded product avatars** — Cart items now display the character's brand color as a gradient on the product initial fallback avatar in the table.

#### 🐛 Bug Fixes

- **Character Management show page** — Back button and Edit button were hardcoded to admin routes, causing 403 errors for staff users. Both are now role-aware.
- **Character Management hero view** — View and Edit buttons were pointing to the customer-facing `/character/{id}` route instead of the management show/edit routes.
- **Payment Management index** — Status filter dropdown now filters in-place (client-side) without triggering a full page reload.
- **Items Management index** — Carousel was missing Prev/Next state updates on search — resolved by switching to table layout with client-side filtering.
- **Cart Management hero** — Quantity increment/decrement was not enforcing the max stock limit correctly when reopening the hero for a different item. Fixed by resetting `currentMaxQty` on every `openHero()` call.
- **Activity Logs** — Long `targetData` descriptions were breaking the carousel card layout. Resolved by truncating to 60 characters in the table and displaying the full text in the hero detail panel.

#### 🎨 UI / Design Changes

- All card/carousel-based index pages replaced with the User Management table design:
  - `PaymentManagementFolder/index.html.twig`
  - `ItemsFolder/index.html.twig`
  - `CartFolder/index.html.twig`
  - `CharacterManagementFolder/index.html.twig`
  - `ActivityLogsFolder/index.html.twig`
- Corresponding CSS files updated to remove carousel-specific styles and add consistent table, badge, and avatar styles.
- Scrollbar hiding applied uniformly across all management page wrappers.
- Hero detail panels now use Symfony-generated route paths (`path()`) instead of manually constructed URL strings, eliminating route drift bugs.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Symfony 7.4 |
| API Layer | API Platform 4.3 |
| Database | MySQL 8.0 |
| ORM | Doctrine 3.6 |
| Auth | Lexik JWT Bundle 3.2 + Google OAuth2 |
| Email | Brevo SMTP |
| PHP | 8.2+ |
| Frontend | Twig, Tailwind CSS (CDN), Stimulus.js, Webpack Encore |

---

## Installation & Setup

### Prerequisites

- PHP 8.2+
- Composer
- Docker & Docker Compose
- Node.js & npm (for frontend assets)
- Symfony CLI (optional but recommended)

### Steps

**1. Clone the repository**

```bash
git clone <repository-url>
cd <project-folder>
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Install JS dependencies and build assets**

```bash
npm install
npm run build
```

**4. Start the database with Docker**

```bash
docker-compose up -d
```

This starts:
- MySQL 8.0 on port `3308` (host)
- phpMyAdmin on port `8083` → http://localhost:8083

**5. Configure environment**

Copy `.env` to `.env.local` and update values as needed (see [Environment Variables](#environment-variables)):

```bash
cp .env .env.local
```

**6. Generate JWT keys**

```bash
php bin/console lexik:jwt:generate-keypair
```

Keys are saved to `config/jwt/private.pem` and `config/jwt/public.pem`.

**7. Run database migrations**

```bash
php bin/console doctrine:migrations:migrate
```

**8. Start the development server**

```bash
symfony server:start
# or
php -S localhost:8000 -t public/
```

The API is now available at `http://localhost:8000/api`.

Interactive API docs (Swagger UI) are at `http://localhost:8000/api/docs`.

---

## Environment Variables

Key variables to configure in `.env.local`:

| Variable | Description | Example |
|---|---|---|
| `APP_ENV` | Application environment | `dev` |
| `APP_SECRET` | Symfony secret key | `<random-string>` |
| `DATABASE_URL` | MySQL connection string | `mysql://root:password@127.0.0.1:3308/NAcessoriesDB?serverVersion=8.0.32` |
| `JWT_SECRET_KEY` | Path to JWT private key | `%kernel.project_dir%/config/jwt/private.pem` |
| `JWT_PUBLIC_KEY` | Path to JWT public key | `%kernel.project_dir%/config/jwt/public.pem` |
| `JWT_PASSPHRASE` | Passphrase for JWT keys | `<passphrase>` |
| `MAILER_DSN` | SMTP connection string | `smtp://user:pass@smtp-relay.brevo.com:587` |
| `MAIL_FROM_ADDRESS` | Sender email address | `noreply@example.com` |
| `GOOGLE_CLIENT_ID` | Google OAuth client ID | `<google-client-id>` |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret | `<google-client-secret>` |
| `CORS_ALLOW_ORIGIN` | Allowed CORS origins (regex) | `^https?://(localhost\|127\.0\.0\.1)(:[0-9]+)?$` |

---

## Authentication

The API uses **JWT Bearer tokens**.

### Login

```
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "secret123"
}
```

**Response 200**

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

Include the token in all subsequent requests:

```
Authorization: Bearer <token>
```

Tokens expire based on the `lexik_jwt_authentication.token_ttl` config (default: 3600 seconds).

---

## API Reference

Base URL: `http://localhost:8000`

All endpoints return `application/json`. Dates are ISO 8601 (e.g. `2026-05-21T10:30:00+00:00`).

---

### Auth

#### POST /api/login

Authenticate and receive a JWT token.

**Request**

```json
{
  "email": "user@example.com",
  "password": "secret123"
}
```

**Response 200**

```json
{
  "token": "eyJ0eXAiOiJKV1Qi..."
}
```

**Response 401**

```json
{
  "code": 401,
  "message": "Invalid credentials."
}
```

---

#### POST /api/register

Register a new customer account. Sends a verification email.

**Request**

```json
{
  "username": "johndoe",
  "email": "john@example.com",
  "password": "secret123"
}
```

| Field | Type | Required | Notes |
|---|---|---|---|
| `username` | string | yes | Display name |
| `email` | string | yes | Must be a valid email |
| `password` | string | yes | Minimum 6 characters |

**Response 201**

```json
{
  "message": "Registration successful. Please verify your email.",
  "email_sent": true,
  "user": {
    "id": 42,
    "username": "johndoe",
    "email": "john@example.com",
    "roles": ["ROLE_CUSTOMER"],
    "isVerified": false,
    "createdAt": "2026-05-21T10:30:00+00:00"
  }
}
```

**Response 409** — email already registered

```json
{ "message": "An account with this email already exists." }
```

**Response 422** — validation error

```json
{ "message": "Password must be at least 6 characters." }
```

---

### Profile

Requires: `Authorization: Bearer <token>` (ROLE_CUSTOMER or higher)

#### GET /api/profile

Get the authenticated user's profile.

**Response 200**

```json
{
  "id": 42,
  "username": "johndoe",
  "email": "john@example.com",
  "displayName": "John Doe",
  "bio": "Accessories collector",
  "zodiacSign": "Aries",
  "roles": ["ROLE_CUSTOMER"],
  "isActive": true,
  "isVerified": true,
  "createdAt": "2026-05-21T10:30:00+00:00"
}
```

---

#### PATCH /api/profile

Update profile fields. All fields are optional — only provided fields are updated.

**Request**

```json
{
  "display_name": "John Doe",
  "bio": "Accessories collector",
  "zodiac_sign": "Aries",
  "new_password": "newpass123",
  "confirm_password": "newpass123"
}
```

| Field | Type | Notes |
|---|---|---|
| `display_name` | string | Optional |
| `bio` | string | Optional |
| `zodiac_sign` | string | Optional |
| `new_password` | string | Optional, min 6 chars |
| `confirm_password` | string | Required if `new_password` is set |

**Response 200** — same shape as GET /api/profile

**Response 422**

```json
{ "message": "Passwords do not match." }
```

---

### Characters

Requires: `Authorization: Bearer <token>` (ROLE_CUSTOMER or higher)

#### GET /api/characters

List all characters, sorted by name. Supports optional search.

**Query Parameters**

| Param | Type | Description |
|---|---|---|
| `search` | string | Filter by name (partial match) |

**Response 200**

```json
[
  {
    "id": 1,
    "name": "Naruto Uzumaki",
    "alignment": "Good",
    "description": "The Seventh Hokage of the Hidden Leaf Village.",
    "createdAt": "2026-03-09T08:37:39+00:00"
  }
]
```

---

#### GET /api/characters/{id}

Get a single character by ID.

**Response 200**

```json
{
  "id": 1,
  "name": "Naruto Uzumaki",
  "alignment": "Good",
  "description": "The Seventh Hokage of the Hidden Leaf Village.",
  "createdAt": "2026-03-09T08:37:39+00:00"
}
```

**Response 404**

```json
{ "message": "Character not found." }
```

---

### Products

Requires: `Authorization: Bearer <token>` (ROLE_CUSTOMER or higher)

#### GET /api/products

List all products, sorted by newest first. Supports search and character filter.

**Query Parameters**

| Param | Type | Description |
|---|---|---|
| `search` | string | Search by product name |
| `character` | integer | Filter by character ID |

**Response 200**

```json
[
  {
    "id": 5,
    "name": "Naruto Headband",
    "productCode": "PROD-001",
    "description": "Replica headband from the Hidden Leaf Village.",
    "price": "299.00",
    "stockQuantity": 50,
    "createdAt": "2026-04-01T11:54:37+00:00",
    "character": {
      "id": 1,
      "name": "Naruto Uzumaki"
    }
  }
]
```

---

#### GET /api/products/{id}

Get a single product by ID.

**Response 200** — same shape as a single item from the list

**Response 404**

```json
{ "message": "Product not found." }
```

---

### Cart

#### GET /api/cart

View the current user's cart. Returns an empty cart for unauthenticated requests.

**Response 200**

```json
{
  "id": 3,
  "updatedAt": "2026-05-21T10:45:00+00:00",
  "items": [
    {
      "id": 12,
      "product": {
        "id": 5,
        "name": "Naruto Headband",
        "price": "299.00"
      },
      "quantity": 2,
      "subtotal": "598.00",
      "addedAt": "2026-05-21T10:40:00+00:00"
    }
  ],
  "totalItems": 2,
  "totalPrice": "598.00"
}
```

---

#### POST /api/cart/add/{id}

Add a product to the cart. Requires authentication.

**Path Parameter**: `id` — product ID

**Request**

```json
{
  "quantity": 2
}
```

| Field | Type | Default | Notes |
|---|---|---|---|
| `quantity` | integer | 1 | Must be ≥ 1 |

**Response 200** — updated cart (same shape as GET /api/cart)

**Response 404**

```json
{ "message": "Product not found." }
```

**Response 422**

```json
{ "message": "Not enough stock. Available: 5" }
```

---

#### PATCH /api/cart/update/{id}

Update the quantity of a cart item. Requires authentication.

**Path Parameter**: `id` — cart item ID

**Request**

```json
{
  "quantity": 3
}
```

**Response 200** — updated cart

**Response 403**

```json
{ "message": "Access denied." }
```

---

#### DELETE /api/cart/remove/{id}

Remove a specific item from the cart. Requires authentication.

**Path Parameter**: `id` — cart item ID

**Response 200** — updated cart

---

#### DELETE /api/cart/clear

Remove all items from the cart. Requires authentication.

**Response 200** — empty cart

---

### Orders

Requires: `Authorization: Bearer <token>` (authenticated users)

#### GET /api/orders

List orders. Customers see only their own orders; admins see all orders.

**Response 200**

```json
[
  {
    "id": 10,
    "orderNumber": "ORD-20260521-0001",
    "status": "pending",
    "totalAmount": "598.00",
    "customerName": "John Doe",
    "customerAddress": "123 Main St",
    "city": "Manila",
    "province": "Metro Manila",
    "deliveryType": "standard",
    "phoneNumber": "09171234567",
    "customer": 42,
    "createdAt": "2026-05-21T10:50:00+00:00",
    "completedAt": null,
    "orderItems": [
      {
        "id": 20,
        "product": 5,
        "quantity": 2,
        "unitPrice": "299.00",
        "subtotal": "598.00"
      }
    ]
  }
]
```

---

#### GET /api/orders/{id}

Get a single order. Customers can only access their own orders.

**Response 200** — same shape as a single item from the list

**Response 403**

```json
{ "message": "Access denied." }
```

---

#### POST /api/orders

Create a new order directly from a product (bypasses cart).

**Request**

```json
{
  "product_id": 5,
  "quantity": 2,
  "customer_name": "John Doe",
  "customer_address": "123 Main St",
  "city": "Manila",
  "province": "Metro Manila",
  "delivery_type": "standard",
  "phone_number": "09171234567"
}
```

| Field | Type | Required | Notes |
|---|---|---|---|
| `product_id` | integer | yes | Must exist and have a character |
| `quantity` | integer | no | Default: 1 |
| `customer_name` | string | yes | |
| `customer_address` | string | yes | |
| `city` | string | yes | |
| `province` | string | yes | |
| `delivery_type` | string | yes | e.g. `standard`, `express` |
| `phone_number` | string | yes | |

**Response 201** — created order

**Response 422**

```json
{ "message": "Insufficient stock." }
```

---

#### PATCH /api/orders/{id}/cancel

Cancel a pending or processing order. Restores stock.

**Response 200** — updated order with `"status": "cancelled"`

**Response 422**

```json
{ "message": "Order cannot be cancelled." }
```

---

#### PATCH /api/orders/{id}/complete

Mark an order as completed. Requires `ROLE_ADMIN`.

**Response 200** — updated order with `"status": "completed"`

---

#### DELETE /api/orders/{id}

Delete an order. Requires `ROLE_ADMIN`. Restores stock if order was not completed.

**Response 204** — no content

---

### Payments

Requires: `Authorization: Bearer <token>` (ROLE_CUSTOMER or higher)

#### GET /api/payments

List the authenticated user's orders with payment info.

**Response 200**

```json
[
  {
    "orderId": 10,
    "orderNumber": "ORD-20260521-0001",
    "status": "pending",
    "totalAmount": "598.00",
    "customerName": "John Doe",
    "customerAddress": "123 Main St",
    "createdAt": "2026-05-21T10:50:00+00:00",
    "items": [
      {
        "id": 20,
        "product": "Naruto Headband",
        "quantity": 2,
        "subtotal": "598.00"
      }
    ]
  }
]
```

---

#### GET /api/payments/{id}

Get payment details for a specific order. Only the order owner can access this.

**Response 200** — same shape as a single item from the list

**Response 403**

```json
{ "message": "Access denied." }
```

---

#### POST /api/payments/charge

Process payment for an order. Moves order status to `processing`.

**Request**

```json
{
  "order_id": 10,
  "payment_method": "gcash"
}
```

| Field | Type | Required | Notes |
|---|---|---|---|
| `order_id` | integer | yes | Must belong to the authenticated user |
| `payment_method` | string | yes | e.g. `card`, `gcash`, `cash`, `bank_transfer` |

**Response 200** — updated payment record

**Response 422**

```json
{ "message": "This order cannot be paid at this stage." }
```

---

## Roles & Permissions

| Role | Inherits | Access |
|---|---|---|
| `ROLE_CUSTOMER` | `ROLE_USER` | Browse products/characters, manage own cart/orders/payments, update profile |
| `ROLE_STAFF` | `ROLE_USER` | Staff dashboard, manage products/orders/stock, view and edit characters |
| `ROLE_ADMIN` | `ROLE_STAFF`, `ROLE_CUSTOMER` | Full access including user management, character management, activity logs, order completion/deletion |

---

## Error Responses

All errors follow this shape:

```json
{
  "message": "Human-readable error description."
}
```

| Status | Meaning |
|---|---|
| 400 | Bad request / invalid JSON |
| 401 | Missing or invalid JWT token |
| 403 | Authenticated but not authorized |
| 404 | Resource not found |
| 409 | Conflict (e.g. duplicate email) |
| 422 | Validation error / business rule violation |
| 500 | Internal server error |
