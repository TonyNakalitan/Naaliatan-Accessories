# NAaccessories — Final Project Presentation

## Project Overview

NAaccessories is a full-stack e-commerce REST API built with **Symfony 7.4** and **API Platform 4.3**. The platform sells game-character-themed accessories, allowing customers to browse products, manage a shopping cart, place orders, and process payments — all through a secure JWT-authenticated API.

---

## Problem Statement

Fans of anime and game characters have limited access to themed merchandise through a structured, API-first platform. NAaccessories solves this by providing:

- A clean REST API for browsing character-linked products
- Secure user registration and authentication
- A full order lifecycle from cart to payment
- Role-based access for customers, staff, and admins

---

## Architecture

```
Client (Browser / Mobile / Postman)
        │
        ▼
  Symfony 7.4 (REST API)
        │
        ├── JWT Authentication (Lexik JWT Bundle)
        ├── API Platform 4.3 (OpenAPI/Swagger docs)
        ├── Doctrine ORM → MySQL 8.0
        ├── Brevo SMTP (email verification)
        └── Google OAuth2 (web UI login)
```

### Key Design Decisions

- **Stateless API** — JWT tokens, no server-side sessions for API consumers
- **Role hierarchy** — ROLE_ADMIN inherits ROLE_STAFF and ROLE_CUSTOMER, keeping access control clean
- **Activity logging** — every significant action is recorded in the `ActivityLog` table for auditability
- **Stock management** — stock is decremented on order creation and restored on cancellation/deletion

---

## Data Model (11 Entities)

| Entity | Purpose |
|---|---|
| User | Authentication, profiles, roles |
| Character | Game characters with alignment (Good/Evil/Neutral) |
| Product | Accessories linked to a character |
| Cart | Per-user shopping cart |
| CartItem | Individual items in a cart |
| Order | Customer orders with delivery info |
| OrderItem | Line items within an order |
| Payment | Payment records (tracked via Order status) |
| Stock | Current stock levels |
| StockTransaction | Stock movement history (RESTOCK/SALE/ADJUSTMENT) |
| ActivityLog | Audit trail for all user actions |

---

## Core Features

### Authentication & Security
- JWT login via `POST /api/login`
- User registration with email verification (Brevo SMTP)
- Google OAuth2 for web UI
- Role-based access control (Customer / Staff / Admin)
- CORS configured for local development

### Product & Character Catalog
- Browse characters with alignment info
- Browse products filtered by character or search term
- Products linked to characters for thematic organization

### Shopping Cart
- Persistent per-user cart
- Add, update, remove items
- Real-time stock validation
- Anonymous cart view (empty) for unauthenticated users

### Order Management
- Create orders directly from a product
- Full lifecycle: `pending` → `processing` → `completed` / `cancelled`
- Stock automatically adjusted on order create/cancel/delete
- Admin can complete or delete orders

### Payments
- Charge an order with a payment method (card, GCash, cash, bank transfer)
- Payment moves order to `processing` status
- Payment history accessible per user

### Admin & Staff Tools
- User management (CRUD)
- Activity log viewer
- Stock transaction management
- Order oversight (all orders, complete/delete)

---

## API Endpoints Summary

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | /api/login | Public | Get JWT token |
| POST | /api/register | Public | Register new account |
| GET | /api/profile | Customer | View profile |
| PATCH | /api/profile | Customer | Update profile |
| GET | /api/characters | Customer | List characters |
| GET | /api/characters/{id} | Customer | Get character |
| GET | /api/products | Customer | List products |
| GET | /api/products/{id} | Customer | Get product |
| GET | /api/cart | Public | View cart |
| POST | /api/cart/add/{id} | Authenticated | Add to cart |
| PATCH | /api/cart/update/{id} | Authenticated | Update cart item |
| DELETE | /api/cart/remove/{id} | Authenticated | Remove cart item |
| DELETE | /api/cart/clear | Authenticated | Clear cart |
| GET | /api/orders | Authenticated | List orders |
| POST | /api/orders | Authenticated | Create order |
| GET | /api/orders/{id} | Authenticated | Get order |
| PATCH | /api/orders/{id}/cancel | Authenticated | Cancel order |
| PATCH | /api/orders/{id}/complete | Admin | Complete order |
| DELETE | /api/orders/{id} | Admin | Delete order |
| GET | /api/payments | Customer | List payments |
| GET | /api/payments/{id} | Customer | Get payment |
| POST | /api/payments/charge | Customer | Process payment |

---

## Demo Flow

### 1. Register & Verify
```bash
POST /api/register
{ "username": "demo", "email": "demo@example.com", "password": "demo123" }
# → Check email for verification link
```

### 2. Login
```bash
POST /api/login
{ "email": "demo@example.com", "password": "demo123" }
# → Copy the token
```

### 3. Browse Products
```bash
GET /api/products
Authorization: Bearer <token>
# → See all products with character info
```

### 4. Add to Cart
```bash
POST /api/cart/add/5
Authorization: Bearer <token>
{ "quantity": 2 }
```

### 5. Place Order
```bash
POST /api/orders
Authorization: Bearer <token>
{
  "product_id": 5,
  "quantity": 2,
  "customer_name": "Demo User",
  "customer_address": "123 Main St",
  "city": "Manila",
  "province": "Metro Manila",
  "delivery_type": "standard",
  "phone_number": "09171234567"
}
```

### 6. Pay
```bash
POST /api/payments/charge
Authorization: Bearer <token>
{ "order_id": 10, "payment_method": "gcash" }
```

---

## Setup in 60 Seconds

```bash
git clone <repo>
cd <project>
composer install
npm install && npm run build
docker-compose up -d
cp .env .env.local   # edit DB/JWT/mail values
php bin/console lexik:jwt:generate-keypair
php bin/console doctrine:migrations:migrate
symfony server:start
```

API: http://localhost:8000/api  
Swagger UI: http://localhost:8000/api/docs  
phpMyAdmin: http://localhost:8083

---

## Technologies Used

- **Symfony 7.4** — PHP framework
- **API Platform 4.3** — REST/OpenAPI layer
- **Doctrine ORM 3.6** — database abstraction
- **MySQL 8.0** — relational database (Docker)
- **Lexik JWT Bundle** — stateless authentication
- **KnpUniversity OAuth2** — Google login
- **Brevo SMTP** — transactional email
- **Nelmio CORS** — cross-origin request handling
- **Webpack Encore + Stimulus.js** — frontend assets
- **Docker Compose** — local infrastructure

---

## What Was Learned

- Designing a clean role-based access control system in Symfony
- Integrating JWT authentication with stateless API endpoints
- Managing stock consistency across order lifecycle events
- Building an audit trail with activity logging
- Structuring a Symfony project with both API and web UI layers
