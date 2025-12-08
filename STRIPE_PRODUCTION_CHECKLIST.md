# 🚀 Stripe Production Deployment Checklist

## ✅ Pre-Deployment Checklist

### 1. 🔑 **API Keys Configuration**
- [ ] **Switch to LIVE keys** in `application/config/stripe.php`
  - Replace `sk_test_...` with `sk_live_...` (Secret Key)
  - Replace `pk_test_...` with `pk_live_...` (Publishable Key)
  - Replace webhook secret with LIVE webhook secret

### 2. 💰 **Price IDs Configuration**
- [ ] **Create LIVE products/prices** in Stripe Dashboard
- [ ] **Update price IDs** in `application/config/stripe.php`
  - Replace test price IDs with live price IDs
  - Example: `price_1SF7EqIhB9G7qunqgiu0Tjvw` → `price_1ABC123...`

### 3. 🌐 **Webhook Configuration**
- [ ] **Create LIVE webhook endpoint** in Stripe Dashboard
  - URL: `https://yourdomain.com/api/stripe_webhook`
  - Events to listen for:
    - `checkout.session.completed`
    - `invoice.paid`
    - `invoice.payment_succeeded`
    - `customer.subscription.created`
    - `customer.subscription.updated`
    - `customer.subscription.deleted`
- [ ] **Copy webhook signing secret** to config file

### 4. 🔒 **SSL/HTTPS Requirements**
- [ ] **Ensure HTTPS is enabled** on production server
- [ ] **Update base_url** in `application/config/config.php` to use HTTPS
- [ ] **Test webhook endpoint** is accessible via HTTPS

### 5. 🗄️ **Database Configuration**
- [ ] **Update database credentials** in `application/config/database.php`
- [ ] **Ensure all Stripe columns exist** in billing table:
  - `stripe_customer_id`
  - `stripe_subscription_id`
  - `stripe_invoice_id`
  - `stripe_session_id`
  - `paid_at`

### 6. 🔧 **Environment Configuration**
- [ ] **Set ENVIRONMENT to 'production'** in `index.php`
- [ ] **Disable debug mode** (`$config['db_debug'] = FALSE`)
- [ ] **Set proper error reporting** for production

## 🧪 Testing Checklist

### 1. **Test Payment Flow**
- [ ] Create test subscription with LIVE keys
- [ ] Verify webhook events are received
- [ ] Check billing record is updated correctly
- [ ] Test payment success/cancel pages

### 2. **Test Webhook Endpoint**
- [ ] Verify webhook URL is accessible: `https://yourdomain.com/api/stripe_webhook`
- [ ] Test webhook signature verification
- [ ] Check webhook logs for errors

### 3. **Test Error Handling**
- [ ] Test failed payments
- [ ] Test invalid webhook signatures
- [ ] Test database connection issues

## 🚨 Common Production Issues & Solutions

### **Issue 1: "Invalid API Key" Error**
**Cause**: Using test keys in production
**Solution**: Switch to live keys in config

### **Issue 2: "Webhook signature verification failed"**
**Cause**: Wrong webhook secret or HTTPS issues
**Solution**: 
- Update webhook secret in config
- Ensure HTTPS is working
- Check webhook URL is correct

### **Issue 3: "Price not found" Error**
**Cause**: Using test price IDs in production
**Solution**: Create live products and update price IDs

### **Issue 4: "Webhook endpoint not accessible"**
**Cause**: Server configuration or firewall issues
**Solution**:
- Check server allows POST requests
- Verify firewall allows Stripe IPs
- Test endpoint accessibility

### **Issue 5: "Database connection failed"**
**Cause**: Wrong database credentials or server issues
**Solution**: Update database config and test connection

## 📋 Step-by-Step Production Setup

### **Step 1: Get Live Keys from Stripe**
1. Login to [Stripe Dashboard](https://dashboard.stripe.com)
2. Switch to "Live" mode (toggle in top-left)
3. Go to "Developers" → "API keys"
4. Copy your live keys

### **Step 2: Create Live Products**
1. Go to "Products" in Stripe Dashboard
2. Create your subscription products
3. Set up pricing (Basic: $50, Standard: $400, Pro: $3000)
4. Copy the price IDs

### **Step 3: Set Up Live Webhook**
1. Go to "Developers" → "Webhooks"
2. Click "Add endpoint"
3. URL: `https://yourdomain.com/api/stripe_webhook`
4. Select events: `checkout.session.completed`, `invoice.paid`, etc.
5. Copy the webhook signing secret

### **Step 4: Update Configuration**
1. Update `application/config/stripe.php` with live keys
2. Update price IDs with live price IDs
3. Update webhook secret

### **Step 5: Test Everything**
1. Test a real payment (small amount)
2. Check webhook logs
3. Verify database updates
4. Test error scenarios

## 🔍 Debugging Production Issues

### **Check Webhook Logs**
- Stripe Dashboard → Webhooks → Your endpoint → Logs
- Look for failed deliveries and error messages

### **Check Application Logs**
- Look in `application/logs/` for PHP errors
- Check web server error logs

### **Test Webhook Manually**
```bash
curl -X POST https://yourdomain.com/api/stripe_webhook \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'
```

### **Verify SSL Certificate**
```bash
curl -I https://yourdomain.com/api/stripe_webhook
```

## 📞 Support Resources

- [Stripe Documentation](https://stripe.com/docs)
- [Stripe Support](https://support.stripe.com)
- [Webhook Testing Guide](https://stripe.com/docs/webhooks/test)
- [Production Checklist](https://stripe.com/docs/keys#test-live-modes)
