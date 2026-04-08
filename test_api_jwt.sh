#!/bin/bash

# API and JWT Authentication Test Script
# This script tests the API Platform endpoints and JWT authentication

BASE_URL="http://localhost:8000/api"
EMAIL="admin@example.com"
PASSWORD="admin123"

echo "=========================================="
echo "Naaliatan's Accessories API Test Script"
echo "=========================================="
echo ""

# Test 1: Check if API is accessible
echo "Test 1: Checking API endpoint accessibility..."
curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/docs" | grep -q "200"
if [ $? -eq 0 ]; then
    echo "✓ API documentation is accessible"
else
    echo "✗ API documentation is not accessible"
    exit 1
fi
echo ""

# Test 2: Test JWT Authentication - Login
echo "Test 2: Testing JWT authentication (login)..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "✗ Failed to obtain JWT token"
    echo "Response: $LOGIN_RESPONSE"
    echo ""
    echo "Note: You may need to create an admin user first."
    echo "Run: php bin/console app:create-admin"
    exit 1
else
    echo "✓ JWT token obtained successfully"
    echo "Token: ${TOKEN:0:50}..."
fi
echo ""

# Test 3: Access protected endpoint with valid token
echo "Test 3: Accessing protected endpoint with valid token..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/products" \
  -H "Authorization: Bearer $TOKEN")

if [ "$HTTP_CODE" -eq 200 ]; then
    echo "✓ Protected endpoint accessible with valid token (HTTP $HTTP_CODE)"
else
    echo "✗ Protected endpoint not accessible (HTTP $HTTP_CODE)"
fi
echo ""

# Test 4: Access protected endpoint without token
echo "Test 4: Accessing protected endpoint without token..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/products")

if [ "$HTTP_CODE" -eq 401 ]; then
    echo "✓ Protected endpoint correctly requires authentication (HTTP $HTTP_CODE)"
else
    echo "✗ Expected 401, got HTTP $HTTP_CODE"
fi
echo ""

# Test 5: Access protected endpoint with invalid token
echo "Test 5: Accessing protected endpoint with invalid token..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/products" \
  -H "Authorization: Bearer invalid_token_here")

if [ "$HTTP_CODE" -eq 401 ]; then
    echo "✓ Invalid token correctly rejected (HTTP $HTTP_CODE)"
else
    echo "✗ Expected 401, got HTTP $HTTP_CODE"
fi
echo ""

# Test 6: List available API resources
echo "Test 6: Listing available API resources..."
echo "Available endpoints:"
echo "  - GET    /api/docs (Public)"
echo "  - POST   /api/login (Public)"
echo "  - GET    /api/users (Admin)"
echo "  - GET    /api/products (Authenticated)"
echo "  - GET    /api/characters (Authenticated)"
echo "  - GET    /api/orders (Admin or Own)"
echo "  - GET    /api/order_items (Admin or Own Order)"
echo "  - GET    /api/carts (Admin or Own)"
echo "  - GET    /api/cart_items (Admin or Own Cart)"
echo "  - GET    /api/stocks (Authenticated)"
echo "  - GET    /api/stock_transactions (Admin)"
echo "  - GET    /api/activity_logs (Admin)"
echo ""

# Test 7: Test getting products list
echo "Test 7: Testing products endpoint..."
PRODUCTS_RESPONSE=$(curl -s "$BASE_URL/products" \
  -H "Authorization: Bearer $TOKEN")

if echo "$PRODUCTS_RESPONSE" | grep -q "hydra:member\|\"id\""; then
    echo "✓ Products endpoint returns valid data"
else
    echo "✗ Products endpoint returned unexpected data"
fi
echo ""

echo "=========================================="
echo "All tests completed!"
echo "=========================================="
echo ""
echo "To start the development server, run:"
echo "  php -S localhost:8000 -t public"
echo ""
echo "Then access the API documentation at:"
echo "  http://localhost:8000/api/docs"
echo ""