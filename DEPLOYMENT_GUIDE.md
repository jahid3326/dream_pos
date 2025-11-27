# Real-time Notifications Deployment Guide for Namecheap Hosting

## Issue

Real-time notifications only show after page reload on production (https://erp.happycoddingit.com/), but work locally.

## Root Causes

1. Shared hosting environments often block WebSocket connections
2. Pusher connections may fail in production due to SSL/TLS configuration issues
3. Missing environment variables in production
4. CORS issues with subdomain deployment

## Solutions Implemented

### 1. Fallback Polling System ✅

-   Added automatic fallback to HTTP polling when WebSockets fail
-   Polls every 5 seconds for new notifications
-   Graceful degradation ensures notifications work even without WebSockets

### 2. Environment Configuration for Production

Create these environment variables in your Namecheap hosting panel:

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://erp.happycoddingit.com

# Broadcasting - Use Pusher for real-time, fallback to polling
BROADCAST_DRIVER=pusher

# Pusher Configuration (Get from https://pusher.com/)
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

# Vite Environment Variables
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Build Assets for Production

```bash
# Run this locally before uploading
npm run build
```

### 4. Files to Upload to Namecheap

Upload these directories/files:

```
- app/
- bootstrap/
- config/
- database/
- public/
- resources/
- routes/
- storage/
- vendor/ (run composer install --no-dev --optimize-autoloader)
- .env (with production values)
- artisan
- composer.json
- composer.lock
```

**DO NOT UPLOAD:**

-   node_modules/
-   .env.local
-   .git/
-   tests/

### 5. Post-Deployment Commands

Run these commands in your hosting cPanel terminal or SSH:

```bash
# Clear all caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Testing the Implementation

### 1. Check Console Logs

Open browser DevTools → Console and look for:

-   "New Notification Received via Echo:" (WebSocket working)
-   "Echo connection failed, falling back to polling" (Polling activated)
-   "Starting fallback polling for notifications" (Polling working)

### 2. Manual Test

1. Create a new shipment/purchase order
2. Check if notification appears immediately in header
3. If not immediate, it should appear within 5 seconds (polling)

## Troubleshooting

### If Notifications Still Don't Work:

1. **Check Environment Variables**

    ```bash
    php artisan config:clear
    php artisan config:cache
    ```

2. **Verify API Routes Work**
   Visit: `https://erp.happycoddingit.com/api/notifications/unread-count`
   Should return JSON with notification count

3. **Check Pusher Dashboard**

    - Log into Pusher.com
    - Check "Debug Console" for connection attempts
    - Verify app credentials match your .env file

4. **Enable Debug Mode Temporarily**
    ```bash
    APP_DEBUG=true
    ```
    Then check for detailed error messages

### Alternative: Pure Polling Mode

If WebSockets continue to fail, you can disable Echo completely and use pure polling:

1. Set in .env:

    ```bash
    BROADCAST_DRIVER=log
    ```

2. The system will automatically fall back to polling

## Performance Considerations

-   Polling every 5 seconds is reasonable for most use cases
-   For high-traffic sites, consider increasing interval to 10-15 seconds
-   WebSocket connections (when working) are more efficient than polling

## Support

If issues persist:

1. Check hosting provider's WebSocket support policy
2. Consider upgrading to VPS hosting for full WebSocket support
3. Contact Namecheap support about WebSocket restrictions
