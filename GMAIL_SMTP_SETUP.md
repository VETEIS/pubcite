# Gmail SMTP Setup Guide - Fix Current Configuration

## 🎯 Goal
Fix Gmail SMTP to work reliably without switching to SendGrid (to avoid 100 emails/day limit).

## 🔍 Root Cause
The issue is **`QUEUE_CONNECTION=sync`** which makes emails execute immediately and block, causing timeouts.

## ✅ Solution: Switch to Database Queue

### Step 1: Update Environment Variables

Add to your `.env` file (or Render environment variables):

```env
QUEUE_CONNECTION=database
```

**Keep your existing Gmail SMTP settings**:
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

### Step 2: Use Gmail App Password (CRITICAL!)

Gmail requires an **App Password** (not your regular password):

1. Go to [Google Account](https://myaccount.google.com/)
2. Click **Security** → Enable **2-Step Verification** (if not enabled)
3. Scroll to **App passwords**
4. Select:
   - App: **Mail**
   - Device: **Other (Custom name)**
   - Name: "PubCite"
5. Click **Generate**
6. Copy the 16-character password (format: `xxxx xxxx xxxx xxxx`)
7. **Remove spaces** and use as `MAIL_PASSWORD`

**Example**:
- Generated: `abcd efgh ijkl mnop`
- Use in .env: `abcdefghijklmnop`

### Step 3: Run Migrations

Ensure the jobs table exists:
```bash
php artisan migrate
```

### Step 4: Set Up Queue Worker on Render

**Option A: Create Background Worker** (Recommended)

1. In Render dashboard, go to your service
2. Click **New** → **Background Worker**
3. Settings:
   - **Name**: `pubcite-queue-worker`
   - **Environment**: Same as your web service
   - **Command**: `php artisan queue:work --queue=emails --tries=3 --timeout=30 --sleep=3 --max-jobs=1000`
   - **Auto-Deploy**: Yes (same as web service)

**Option B: Add to Dockerfile/Startup Script** (If you can't create separate worker)

Add to your startup script:
```bash
# Start queue worker in background
php artisan queue:work --queue=emails --tries=3 --timeout=30 --sleep=3 --max-jobs=1000 &
```

### Step 5: Test Configuration

After deployment, test:
```bash
php artisan tinker
```

Then:
```php
Mail::raw('Test email from Gmail SMTP', function ($message) {
    $message->to('your-email@gmail.com')
            ->subject('Gmail SMTP Test');
});
```

Check:
- Laravel logs for email activity
- Your inbox (and spam folder)
- Queue worker logs

## 🔧 Improvements Made

### 1. Retry Logic
- All email classes now retry 3 times on failure
- Exponential backoff: 1min, 5min, 15min
- Failed jobs logged with full details

### 2. Timeout Reduction
- Changed from 60s to 30s (faster failure detection)
- Configurable via `MAIL_TIMEOUT` env variable

### 3. Error Handling
- `failed()` method added to all email classes
- Comprehensive error logging
- Can retry failed jobs manually

## 📊 How It Works

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
**Benefits**: 
- ✅ Non-blocking (immediate HTTP response)
- ✅ Automatic retries on failure
- ✅ Better error handling
- ✅ Can monitor queue status

## 🚀 Render Deployment Checklist

1. ✅ Add `QUEUE_CONNECTION=database` to environment variables
2. ✅ Verify Gmail App Password is set (not regular password)
3. ✅ Create Background Worker OR add to startup script
4. ✅ Run migrations: `php artisan migrate`
5. ✅ Redeploy service
6. ✅ Test email sending

## 🔍 Monitoring & Debugging

### Check Queue Status:
```bash
# View pending jobs
php artisan queue:work --queue=emails --tries=3 --timeout=30

# View failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry <job-id>

# Clear failed jobs
php artisan queue:flush
```

### Check Logs:
- Laravel logs: `storage/logs/laravel.log`
- Look for "Email sending failed after retries"
- Check queue worker output

## 🐛 Troubleshooting

### Issue: "Jobs table doesn't exist"
**Solution**: 
```bash
php artisan migrate
```

### Issue: "Authentication failed"
**Solution**: 
- Use Gmail **App Password** (not regular password)
- Enable 2-Step Verification first
- Verify `MAIL_USERNAME` is full Gmail address
- Remove spaces from App Password

### Issue: "Connection timeout"
**Solution**:
- Check if port 587 is accessible from Render
- Try port 465 with `MAIL_ENCRYPTION=ssl`
- Verify queue worker is running
- Check network/firewall settings

### Issue: "Queue worker not processing"
**Solution**:
- Verify worker is running: Check Render dashboard
- Check `jobs` table for pending jobs
- Restart queue worker
- Check worker logs for errors

### Issue: "Emails still timing out"
**Solution**:
- Verify `QUEUE_CONNECTION=database` is set
- Check queue worker is actually running
- Verify jobs are being stored in `jobs` table
- Check worker logs for connection errors

## 📝 Complete Environment Variables

```env
# Queue Configuration (REQUIRED)
QUEUE_CONNECTION=database

# Gmail SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password-no-spaces
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="PubCite System"
MAIL_TIMEOUT=30
```

## ✅ Benefits

- ✅ **Unlimited emails** (Gmail's limit is much higher)
- ✅ **No blocking** - HTTP response immediate
- ✅ **Automatic retries** - 3 attempts with exponential backoff
- ✅ **Better error handling** - Failed jobs logged
- ✅ **Works with existing Gmail** - No new service needed
- ✅ **No daily limits** - Perfect for testing

## 🎯 Next Steps

1. Set `QUEUE_CONNECTION=database` in environment
2. Get Gmail App Password
3. Create Background Worker on Render
4. Redeploy and test

The code changes are already done - you just need to configure the environment and run the queue worker!

