# 🚀 Olcay KAHRAMAN — Kişisel Portfolio Sitesi

Modern, responsive ve dinamik bir kişisel portfolyo web sitesi. PHP + MySQL tabanlı backend API, vanilla JavaScript ile geliştirilmiş frontend ve harici API entegrasyonu içermektedir.

[![GitHub](https://img.shields.io/badge/GitHub-OlcayKAHRAMAN2005-181717?style=flat&logo=github)](https://github.com/OlcayKAHRAMAN2005/portfolio)
[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat)](LICENSE)

---

## 📋 Proje Hakkında

Bu proje, Kırşehir Ahi Evran Üniversitesi Bilgisayar Programcılığı öğrencisi **Olcay KAHRAMAN** tarafından geliştirilmiştir. Projeler, sertifikalar ve iletişim formu gibi bilgileri dinamik olarak yönetebilen tam işlevli bir portfolyo sitesidir. Frontend tamamen Vanilla HTML/CSS/JS ile yazılmış; backend ise PHP + MySQL (PDO) ile RESTful bir API mimarisine sahiptir.

---

## ✨ Özellikler

### Kullanıcı Arayüzü
- 🌙 **Dark / Light tema** — tercih localStorage'a kaydedilir
- 📱 **Tam responsive tasarım** — hamburger menü dahil tüm ekran boyutları desteklenir
- 🔍 **Gerçek zamanlı arama** — proje adı, açıklama ve etiketlerde anlık filtreleme
- 🏷️ **Kategori filtreleme** — Frontend / Backend / Mobile
- 🔤 **Sıralama seçenekleri** — A→Z, Z→A ve Favoriler önce
- ⭐ **Favori sistemi** — seçilen projeler localStorage'a kaydedilir

### Harici API Entegrasyonu
- 🌤️ **Open-Meteo API** — ücretsiz, API anahtarı gerektirmeyen hava durumu servisi
  - Ana sayfada Kırşehir için anlık sıcaklık, hissedilen sıcaklık, rüzgar hızı ve nem gösterilir

### Backend & Veritabanı
- 🔐 **Kullanıcı oturum yönetimi** — PHP native `$_SESSION` ile
- 💬 **İletişim formu** — mesajlar MySQL'e kaydedilir, admin panelinden görüntülenebilir
- 🗄️ **Proje ve sertifika CRUD** — API üzerinden tam yönetim
- 🛡️ **Güvenli sorgu yapısı** — PDO Prepared Statements ile SQL Injection koruması
- 🔒 **XSS koruması** — tüm HTML çıktıları `escHtml()` ile sanitize edilir
- 🔑 **Şifre güvenliği** — bcrypt (`password_hash()`) ile hashleme

---

## 🛠️ Kullanılan Teknolojiler

| Teknoloji | Sürüm | Amaç |
|-----------|-------|------|
| **PHP** | 7.4+ | Backend API, oturum yönetimi |
| **MySQL / MariaDB** | 10.4+ | İlişkisel veritabanı |
| **PDO** | — | Güvenli veritabanı erişimi (Prepared Statements) |
| **HTML5** | — | Semantik sayfa yapısı |
| **CSS3** | — | Tasarım, animasyon, responsive layout |
| **JavaScript (ES6+)** | — | DOM manipülasyonu, Fetch API, dinamik içerik |
| **Open-Meteo API** | — | Ücretsiz hava durumu verisi |
| **Font Awesome** | 6.5.1 | İkon kütüphanesi |
| **Google Fonts (Poppins)** | — | Tipografi |

---

## 📁 Proje Yapısı

```
portfolio_final/
├── api/
│   ├── db.php              # PDO bağlantısı, yardımcı fonksiyonlar
│   ├── auth.php            # Kullanıcı kimlik doğrulama (deprecated)
│   ├── contact.php         # İletişim mesajları (gönder / listele / sil)
│   ├── projects.php        # Proje CRUD işlemleri
│   └── certificates.php    # Sertifika CRUD işlemleri
├── index.html              # Ana sayfa — hava durumu widget'ı
├── projects.html           # Projeler — arama, filtre, sıralama
├── about.html              # Hakkımda sayfası
├── resume.html             # Özgeçmiş (yazdırma desteği)
├── contact.html            # İletişim formu
├── auth.html               # Giriş / Kayıt sayfası
├── certificates.html       # Sertifikalar sayfası
├── install.php             # Otomatik veritabanı kurulum sihirbazı
├── script.js               # Ortak JavaScript modülü
├── style.css               # Global stiller + dark/light tema
├── project.json            # Statik proje verisi (fallback)
├── portfolio_db.sql        # Veritabanı yedeği (phpMyAdmin dump)
└── README.md
```

---

## 🗃️ Veritabanı Şeması

```sql
-- Kullanıcılar
users (id, name, email, password [bcrypt], role, created_at)

-- İletişim mesajları
messages (id, name, email, subject, message, is_read, created_at)

-- Projeler
projects (id, title, badge, image, category, description, tags, github_url, created_at)

-- Sertifikalar
certificates (id, title, issuer, category, date_earned, credential_url, description, created_at)
```

---

## 🌐 API Endpoint'leri

| Metot | Endpoint | Açıklama |
|-------|----------|----------|
| POST | `api/contact.php?action=send` | Yeni mesaj gönder |
| GET | `api/contact.php?action=list` | Mesajları listele |
| DELETE | `api/contact.php?action=delete` | Mesaj sil |
| GET | `api/projects.php?action=list` | Projeleri listele |
| POST | `api/projects.php?action=add` | Yeni proje ekle |
| POST | `api/projects.php?action=update` | Proje güncelle |
| DELETE | `api/projects.php?action=delete` | Proje sil |
| GET | `api/certificates.php?action=list` | Sertifikaları listele |
| POST | `api/certificates.php?action=add` | Sertifika ekle |
| DELETE | `api/certificates.php?action=delete` | Sertifika sil |

---

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7+ / MariaDB 10.4+
- Web sunucusu (Apache / Nginx) veya XAMPP / WAMP

### Adım 1 — Dosyaları Yükle
FTP veya cPanel File Manager ile tüm dosyaları hosting'in `public_html` (veya `htdocs`) dizinine yükleyin.

### Adım 2 — Veritabanı Oluştur
cPanel → MySQL Databases bölümünden:
1. Yeni bir veritabanı oluşturun (örn. `portfolio_db`)
2. Yeni bir kullanıcı oluşturun ve veritabanına bağlayın
3. Tüm yetkileri (ALL PRIVILEGES) verin

### Adım 3 — Bağlantı Ayarları
`api/db.php` dosyasını açıp kendi bilgilerinizle güncelleyin:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'kullanici_adi');
define('DB_PASS', 'sifreniz');
define('DB_NAME', 'portfolio_db');
```

### Adım 4 — Otomatik Kurulum
Tarayıcıda aşağıdaki adresi açın; tablolar ve örnek veriler otomatik oluşturulur:

```
https://siteniz.com/install.php
```

### Adım 5 — Güvenlik
Kurulum tamamlandıktan sonra `install.php` dosyasını sunucudan silin:

```bash
rm install.php
```

Alternatif olarak `portfolio_db.sql` dosyasını phpMyAdmin üzerinden import ederek de kurulum yapabilirsiniz.

---

## 🔒 Güvenlik

- Tüm veritabanı sorguları **PDO Prepared Statements** ile yazılmıştır (SQL Injection koruması)
- Kullanıcı şifreleri `password_hash()` fonksiyonu ile **bcrypt** olarak saklanır
- HTML çıktıları `escHtml()` yardımcı fonksiyonu ile sanitize edilir (**XSS koruması**)
- Oturum yönetimi PHP'nin native `$_SESSION` mekanizması ile sağlanır
- CORS başlıkları yalnızca geliştirme ortamı için açıktır; production'da kısıtlanmalıdır

---

## 📸 Öne Çıkan Projeler

| Proje | Teknoloji | Açıklama |
|-------|-----------|----------|
| **StreamAtlas** | HTML/CSS/JS + TMDB API | Film & dizi keşif platformu arayüzü |
| **GSWEB** | C# / ASP.NET Core MVC / SQL Server | Galatasaray taraftar içerik yönetim sistemi |
| **Portfolio Site** | HTML / CSS / JS / PHP / MySQL | Bu site |
| **Mobile App** | Flutter / Dart | Cross-platform mobil uygulama |

---

## 👤 Geliştirici

**Olcay KAHRAMAN**
Kırşehir Ahi Evran Üniversitesi — Bilgisayar Programcılığı

- 🐙 GitHub: [@OlcayKAHRAMAN2005](https://github.com/OlcayKAHRAMAN2005)
- 💼 LinkedIn: [olcay-kahraman2005](https://linkedin.com/in/olcay-kahraman2005)
- 📧 E-posta: olcaykahraman2405@gmail.com


