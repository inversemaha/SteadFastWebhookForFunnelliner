# Steadfast Webhook Integration

Simple Laravel webhook integration to receive real-time delivery updates from Steadfast Courier.

## Requirements

- **PHP**: ^8.2
- **Laravel Framework**: ^12.0
- **MySQL/MariaDB**: Database server
- **Composer**: Dependency manager

## Setup

### 1. Configuration
Set your webhook token in `.env`:
```env
STEADFAST_WEBHOOK_TOKEN=test_webhook_token_123
```

Or update the token in `config/steadfast.php` file.

### 2. Database
Run migrations to create orders table:
```bash
php artisan migrate
```

### 3. Ngrok URL
Configure this endpoint in your Steadfast dashboard:
```
https://brittany-uneduced-laurinda.ngrok-free.dev/api/webhooks/steadfast
```

For local development, use ngrok to expose your Laravel server:
```bash
php artisan serve
ngrok http 8000
```

## Webhook Details

### Endpoint
- **URL**: `/api/webhooks/steadfast`
- **Method**: `POST`
- **Authentication**: Bearer Token
- **Content-Type**: `application/json`

### Supported Notifications

#### 1. Delivery Status
Updates order status, COD amount, and delivery charges.

**Payload Example:**
```json
{
    "notification_type": "delivery_status",
    "consignment_id": 12345,
    "invoice": "INV-67890",
    "cod_amount": 1500.00,
    "status": "delivered",
    "delivery_charge": 100.00,
    "tracking_message": "Package delivered successfully",
    "updated_at": "2025-03-02 12:45:30"
}
```

#### 2. Tracking Update
Updates only tracking message and timestamp.

**Payload Example:**
```json
{
    "notification_type": "tracking_update",
    "consignment_id": 12345,
    "invoice": "INV-67890",
    "tracking_message": "Package arrived at sorting center",
    "updated_at": "2025-03-02 13:15:00"
}
```

### Responses

**Success:**
```json
{
    "status": "success",
    "message": "Webhook received successfully."
}
```

**Error:**
```json
{
    "status": "error",
    "message": "Error description"
}
```

## Files & Business Logic

### Route Configuration
- **File**: `routes/api.php`
- **Endpoint**: `POST /api/webhooks/steadfast`
- **Handler**: `SteadfastWebhookController@handleSteadFastWebhook`

### Business Logic Location
- **Main Controller**: `app/Http/Controllers/SteadfastWebhookController.php`
  - `handleSteadFastWebhook()` - Main webhook handler with authentication
  - `validatePayload()` - Laravel validation for webhook data
  - `processPayload()` - **Business logic for updating orders**
  
### Key Components
- **Model**: `app/Models/Order.php` - Database interactions
- **Config**: `config/steadfast.php` - Webhook token configuration
- **Migration**: `database/migrations/2025_10_23_140011_create_orders_table.php` - Database schema

### Customize Business Logic
To modify how orders are updated, edit the `processPayload()` method in:
```
app/Http/Controllers/SteadfastWebhookController.php
```

## Testing

### Case 1: Delivery Status
Test delivery status webhook with curl:
```bash
curl -X POST https://brittany-uneduced-laurinda.ngrok-free.dev/api/webhooks/steadfast \
  -H "Authorization: Bearer test_webhook_token_123" \
  -H "Content-Type: application/json" \
  -d '{
    "notification_type": "delivery_status",
    "consignment_id": 12345,
    "invoice": "INV-67890",
    "cod_amount": 1500.00,
    "status": "delivered",
    "delivery_charge": 100.00,
    "tracking_message": "Package delivered successfully",
    "updated_at": "2025-03-02 12:45:30"
  }'
```

### Case 2: Tracking Update
Test tracking update webhook with curl:
```bash
curl -X POST https://brittany-uneduced-laurinda.ngrok-free.dev/api/webhooks/steadfast \
  -H "Authorization: Bearer test_webhook_token_123" \
  -H "Content-Type: application/json" \
  -d '{
    "notification_type": "tracking_update",
    "consignment_id": 12345,
    "invoice": "INV-67890",
    "tracking_message": "Package arrived at sorting center",
    "updated_at": "2025-03-02 13:15:00"
  }'
```

## Future Enhancements

### Real-time Updates with Pusher
- **Pusher Integration**: Broadcast webhook events to frontend in real-time
- **Live Dashboard**: Instant order status updates in user interface
- **Event Broadcasting**: Push delivery notifications to connected clients

## References

- **Steadfast Webhook Documentation**: https://www.steadfast.com.bd/user/webhook/add