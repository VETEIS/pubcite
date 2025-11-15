# Fixing Gmail SMTP Setup

## 🔍 Root Cause Analysis

The email timeout issue is caused by:
1. **`QUEUE_CONNECTION=sync`** - Emails are sent synchronously, blocking the request
2. **Gmail SMTP connection timeout** - Takes 60+ seconds to fail
3. **No retry mechanism** - Fails permanently on first timeout

## ✅ Solution: Switch to Database Queue

This allows emails to be sent asynchronously without blocking the request.

### Step 1: Ensure Jobs Table Exists

The migration should already exist. Run:
```bash
php artisan migrate
```

This creates the `jobs` table for queuing.

### Step 2: Update Environment Variables

Add to your `.env` file (or Render environment variables):

```env
QUEUE_CONNECTION=database
```

**Keep your Gmail SMTP settings**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PubCite System"
MAIL_TIMEOUT=30
```

### Step 3: Use Gmail App Password (Important!)

Gmail requires an **App Password** (not your regular password) for SMTP:

1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click **Security** → **2-Step Verification** (enable if not already)
3. Scroll down to **App passwords**
4. Select **Mail** and **Other (Custom name)**
5. Enter "PubCite" as the name
6. Click **Generate**
7. Copy the 16-character password (no spaces)
8. Use this as `MAIL_PASSWORD` in your `.env`

**Important**: 
- Use the App Password, NOT your regular Gmail password
- App passwords are 16 characters, no spaces
- Format: `xxxx xxxx xxxx xxxx` (remove spaces when using)

### Step 4: Run Queue Worker

On **Render**, you need to run a queue worker. Add this to your **Render Service**:

**Option A: Add as Background Worker** (Recommended)
1. In Render dashboard, create a new **Background Worker**
2. Command: `php artisan queue:work --queue=emails --tries=3 --timeout=30`
3. This runs continuously and processes email jobs

**Option B: Add to Existing Service** (If you can't create a separate worker)
1. Add to your Dockerfile or startup script:
```bash
php artisan queue:work --queue=emails --tries=3 --timeout=30 &
```
2. This runs the queue worker in the background

### Step 5: Test Email

After deployment, test email sending:
```bash
php artisan tinker
```

Then:
```php
Mail::raw('Test email', function ($message) {
    $message->to('your-email@gmail.com')
            ->subject('Gmail SMTP Test');
});
```

Check:
- Browser console for any errors
- Laravel logs for email activity
- Your inbox (and spam folder)

## 🔧 Additional Improvements Made

### 1. Retry Logic Added
- Emails will retry up to 3 times on failure
- Exponential backoff: 1min, 5min, 15min
- Failed jobs logged for debugging

### 2. Timeout Reduced
- Changed from 60 seconds to 30 seconds
- Faster failure detection
- Configurable via `MAIL_TIMEOUT` env variable

### 3. Error Handling
- Failed email jobs are logged with full details
- Can retry failed jobs manually: `php artisan queue:retry <job-id>`

## 📊 How It Works Now

### Before (Sync Queue):
```
Request → Mail::queue() → Execute NOW → SMTP timeout (60s) → Error
```
**Problem**: Blocks request, times out

### After (Database Queue):
```
Request → Mail::queue() → Store in DB → HTTP response (immediate)
         ↓
Queue Worker → Pick up job → SMTP connection → Success/Retry
```
**Benefits**: Non-blocking, retries on failure, faster response

## 🚀 Render Deployment Steps

1. **Add Environment Variable**:
   - `QUEUE_CONNECTION=database`

2. **Create Background Worker** (Recommended):
   - Service Type: **Background Worker**
   - Command: `php artisan queue:work --queue=emails --tries=3 --timeout=30`
   - This runs continuously

3. **Or Add to Main Service**:
   - Modify your startup script to run queue worker in background
   - Less ideal but works if you can't create separate worker

4. **Redeploy** your service

## 🔍 Monitoring

### Check Queue Status:
```bash
php artisan queue:work --queue=emails --tries=3 --timeout=30
```

### View Failed Jobs:
```bash
php artisan queue:failed
```

### Retry Failed Jobs:
```bash
php artisan queue:retry all
```

### Clear Failed Jobs:
```bash
php artisan queue:flush
```

## 🐛 Troubleshooting

### Issue: "Jobs table doesn't exist"
**Solution**: Run `php artisan migrate`

### Issue: "Queue worker not processing"
**Solution**: 
- Check if worker is running: `ps aux | grep queue:work`
- Check `jobs` table for pending jobs
- Restart queue worker

### Issue: "Authentication failed"
**Solution**: 
- Use Gmail App Password (not regular password)
- Enable 2-Step Verification first
- Verify `MAIL_USERNAME` is your full Gmail address

### Issue: "Connection timeout"
**Solution**:
- Check if port 587 is open on Render
- Try port 465 with `MAIL_ENCRYPTION=ssl`
- Verify Gmail SMTP is accessible from Render's network

## 📝 Environment Variables Summary

```env
# Queue Configuration
QUEUE_CONNECTION=database

# Gmail SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PubCite System"
MAIL_TIMEOUT=30
```

## ✅ Benefits

- ✅ **Unlimited emails** (Gmail's limit is much higher than SendGrid free tier)
- ✅ **No blocking** - HTTP response immediate
- ✅ **Automatic retries** - 3 attempts with backoff
- ✅ **Better error handling** - Failed jobs logged
- ✅ **Works with existing Gmail account** - No new service needed

