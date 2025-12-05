<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CasperVPN Admin - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 32px;
        }
        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .value {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ CasperVPN Admin Panel</h1>
        <div class="info">
            <div class="info-item">
                <span class="label">Framework:</span>
                <span class="value">Laravel {{ app()->version() }}</span>
            </div>
            <div class="info-item">
                <span class="label">PHP Version:</span>
                <span class="value">{{ PHP_VERSION }}</span>
            </div>
            <div class="info-item">
                <span class="label">Environment:</span>
                <span class="value">{{ app()->environment() }}</span>
            </div>
            <div class="info-item">
                <span class="label">Status:</span>
                <span class="value" style="color: #28a745;">✓ Running</span>
            </div>
        </div>
    </div>
</body>
</html>
