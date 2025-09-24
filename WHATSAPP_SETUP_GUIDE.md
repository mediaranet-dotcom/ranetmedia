# 📱 WhatsApp Integration Setup Guide

## 🎯 Overview
Sistem WhatsApp gratis untuk 1000+ pelanggan menggunakan multiple WhatsApp Web API instances dengan load balancing dan rate limiting.

## 🚀 Quick Setup

### 1. 📋 Prerequisites
```bash
# Install Docker & Docker Compose
# Windows: Download Docker Desktop
# Linux: 
sudo apt update
sudo apt install docker.io docker-compose

# Verify installation
docker --version
docker-compose --version
```

### 2. 🔧 Environment Configuration
```bash
# Add to .env file
WHATSAPP_API_URL_1=http://localhost:3001
WHATSAPP_API_URL_2=http://localhost:3002
WHATSAPP_API_URL_3=http://localhost:3003

# WhatsApp settings
WHATSAPP_DAILY_LIMIT=200
WHATSAPP_HOURLY_LIMIT=20
WHATSAPP_ENABLED=true
```

### 3. 🐳 Start WhatsApp API Servers
```bash
# Start all WhatsApp API instances
docker-compose -f docker-compose.whatsapp.yml up -d

# Check status
docker-compose -f docker-compose.whatsapp.yml ps

# View logs
docker-compose -f docker-compose.whatsapp.yml logs -f
```

### 4. 📱 Setup WhatsApp Sessions

#### Instance 1:
1. Open: http://localhost:3001
2. Create session: `session1`
3. Scan QR code with WhatsApp 1
4. Wait for "Connected" status

#### Instance 2:
1. Open: http://localhost:3002
2. Create session: `session2`
3. Scan QR code with WhatsApp 2
4. Wait for "Connected" status

#### Instance 3:
1. Open: http://localhost:3003
2. Create session: `session3`
3. Scan QR code with WhatsApp 3
4. Wait for "Connected" status

## 🧪 Testing

### 1. Test WhatsApp Service
```bash
# Test individual message
php artisan tinker

# In tinker:
$whatsapp = app(App\Services\WhatsAppService::class);
$customer = App\Models\Customer::first();
$invoice = App\Models\Invoice::first();
$result = $whatsapp->sendInvoiceNotification($invoice);
var_dump($result);
```

### 2. Test Commands
```bash
# Test dry run
php artisan whatsapp:send invoice --dry-run --limit=5

# Test actual sending
php artisan whatsapp:send invoice --limit=5

# Test reminders
php artisan whatsapp:send reminder --dry-run

# Test overdue
php artisan whatsapp:send overdue --limit=10
```

### 3. Check Usage Stats
```bash
php artisan tinker

# In tinker:
$whatsapp = app(App\Services\WhatsAppService::class);
$stats = $whatsapp->getUsageStats();
print_r($stats);
```

## 📊 Dashboard Integration

### Add WhatsApp Widget to Dashboard
```php
// In app/Filament/Pages/Dashboard.php
public function getWidgets(): array
{
    return [
        \Filament\Widgets\AccountWidget::class,
        \App\Filament\Widgets\CoverageAreaStatsWidget::class,
        \App\Filament\Widgets\AutoInvoiceStatsWidget::class,
        \App\Filament\Widgets\WhatsAppStatsWidget::class, // Add this
    ];
}
```

## ⏰ Automation Setup

### Add to Console Kernel
```php
// In app/Console/Kernel.php schedule method

// Send WhatsApp invoice notifications (daily 10 AM)
$schedule->command('whatsapp:send invoice --limit=100')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->runInBackground();

// Send WhatsApp payment reminders (daily 2 PM)
$schedule->command('whatsapp:send reminder --limit=50')
    ->dailyAt('14:00')
    ->withoutOverlapping()
    ->runInBackground();

// Send WhatsApp overdue reminders (daily 4 PM)
$schedule->command('whatsapp:send overdue --limit=50')
    ->dailyAt('16:00')
    ->withoutOverlapping()
    ->runInBackground();
```

## 📱 WhatsApp Account Strategy

### For 1000 Customers:
- **3 WhatsApp Numbers** (Business/Personal)
- **200 messages/day per number** = 600 total/day
- **20 messages/hour per number** = 60 total/hour
- **Smart load balancing** across accounts
- **Priority-based sending** (overdue first)

### Message Distribution:
- **Daily invoices**: ~100 messages
- **Payment reminders**: ~50 messages  
- **Overdue alerts**: ~30 messages
- **Total**: ~180 messages/day (well within limits)

## 🔧 Troubleshooting

### Common Issues:

#### 1. WhatsApp Session Disconnected
```bash
# Restart specific instance
docker-compose -f docker-compose.whatsapp.yml restart whatsapp-api-1

# Re-scan QR code
# Open http://localhost:3001 and scan again
```

#### 2. Rate Limit Reached
```bash
# Check usage stats
php artisan tinker
$whatsapp = app(App\Services\WhatsAppService::class);
print_r($whatsapp->getUsageStats());

# Wait for hourly reset or use different account
```

#### 3. Messages Not Sending
```bash
# Check Docker logs
docker-compose -f docker-compose.whatsapp.yml logs whatsapp-api-1

# Check Laravel logs
tail -f storage/logs/laravel.log | grep -i whatsapp

# Test API directly
curl -X POST http://localhost:3001/api/sendText \
  -H "Content-Type: application/json" \
  -d '{"session":"session1","number":"6281234567890","text":"Test message"}'
```

## 📈 Scaling for Growth

### When you reach 2000+ customers:

#### Option 1: Add More Accounts
- Add 2 more WhatsApp numbers
- Update docker-compose.yml
- Increase daily limits

#### Option 2: Upgrade to Official API
- WhatsApp Business API
- ~Rp 150/message
- Unlimited sending
- Professional templates

## 🎯 Best Practices

### 1. Message Timing
- **Morning (10 AM)**: New invoices
- **Afternoon (2 PM)**: Payment reminders
- **Evening (4 PM)**: Overdue alerts
- **Avoid**: Late night, early morning

### 2. Message Content
- **Keep short** (under 160 chars when possible)
- **Use emojis** for better engagement
- **Include payment methods**
- **Add contact info**

### 3. Rate Limiting
- **Spread messages** throughout the day
- **Monitor usage** via dashboard
- **Prioritize urgent** messages
- **Queue non-urgent** messages

### 4. Monitoring
- **Check dashboard** daily
- **Monitor Docker containers**
- **Watch Laravel logs**
- **Track delivery rates**

## 🚀 Production Deployment

### 1. Server Setup
```bash
# On production server
git clone your-repo
cd your-project

# Start WhatsApp services
docker-compose -f docker-compose.whatsapp.yml up -d

# Setup sessions (scan QR codes)
# Configure cron jobs for automation
```

### 2. Backup Strategy
```bash
# Backup WhatsApp sessions
tar -czf whatsapp-sessions-backup.tar.gz whatsapp-data/

# Restore if needed
tar -xzf whatsapp-sessions-backup.tar.gz
```

## 📞 Support

### If you need help:
1. Check logs first
2. Test with single message
3. Verify WhatsApp sessions
4. Check rate limits
5. Contact support if needed

---

**🎉 Your WhatsApp integration is ready for 1000+ customers with smart load balancing and rate limiting!**
