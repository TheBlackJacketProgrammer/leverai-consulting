<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Subscription Activated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .subscription-details {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .subscription-details h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button-secondary {
            display: inline-block;
            padding: 10px 25px;
            background-color: #95a5a6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Welcome! Your Subscription is Active</h1>
        </div>
        
        <div class="content">
            <p>Dear <?php echo htmlspecialchars($name); ?>,</p>
            
            <p>Congratulations! Your subscription has been successfully activated. We're excited to have you on board!</p>
            
            <div class="subscription-details">
                <h3>Your Subscription Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Plan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(ucfirst($plan)); ?> Plan</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hours Allocated:</span>
                    <span class="detail-value"><?php echo number_format($hours, 0); ?> hour(s)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value" style="color: #27ae60; font-weight: bold;">Active</span>
                </div>
            </div>
            
            <p>You can now start using your allocated hours. Log in to your account to begin using our services.</p>
            
            <div style="text-align: center;">
                <a href="<?php echo $site_url; ?>" class="button">Go to Dashboard</a>
            </div>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            
            <p>Thank you for choosing <?php echo htmlspecialchars($site_name); ?>!</p>
            
            <p>Best regards,<br>
            The <?php echo htmlspecialchars($site_name); ?> Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>

