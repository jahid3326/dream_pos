<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Shipment Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 40px 30px;
        }

        .shipment-details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
        }

        .detail-value {
            color: #212529;
            text-align: right;
            flex-grow: 1;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }

        .icon {
            width: 24px;
            height: 24px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        .alert {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🚢 New Shipment Created</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">You have been assigned a new shipment to process</p>
        </div>

        <div class="content">
            <p style="font-size: 16px; margin-bottom: 20px;">
                Hello <strong>{{ $user->name }}</strong>,
            </p>

            <p>A new shipment has been created from a purchase order and requires your attention. Please review the
                details below and begin processing as soon as possible.</p>

            <div class="shipment-details">
                <div class="detail-row">
                    <span class="detail-label">Shipment Number:</span>
                    <span class="detail-value"><strong>{{ $shipment->shipment_number }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">{{ $shipment->customer->user->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Purchase Order:</span>
                    <span class="detail-value">{{ $shipment->purchase->purchase_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Created Date:</span>
                    <span class="detail-value">{{ $shipment->shipment_date->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span
                            style="background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                            Pending Processing
                        </span>
                    </span>
                </div>
            </div>

            <div class="alert">
                <strong>Action Required:</strong> Please add shipping costs, upload required documents, and update
                delivery estimates for this shipment.
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('shipments.show', $shipment) }}" class="btn">
                    View Shipment Details
                </a>
            </div>

            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                This shipment was automatically created from purchase order processing. If you have any questions,
                please contact the sales team.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                <strong>Dream POS</strong> - Shipment Management System
            </p>
            <p style="margin: 5px 0 0 0;">
                This is an automated notification. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>

</html>
