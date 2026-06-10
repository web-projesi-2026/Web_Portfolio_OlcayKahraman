<?php
/**
 * db.php — Veritabanı Bağlantısı
 * 
 * Bu dosyayı kendi sunucunuza göre düzenleyin:
 *   - XAMPP/WAMP kullanıyorsanız: host=localhost, user=root, pass=''
 *   - cPanel paylaşımlı hosting: host, user, pass bilgilerini hosting panelinden alın
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ← Kendi kullanıcı adınızı yazın
define('DB_PASS', '');           // ← Kendi şifrenizi yazın
define('DB_NAME', 'portfolio_db');
define('DB_CHARSET', 'utf8mb4');

// PDO bağlantısı (en güvenli yöntem)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST, DB_NAME, DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()
        ]));
    }

    return $pdo;
}

// =============================================
// TABLOLARI OLUŞTUR (ilk kurulumda çalışır)
// =============================================
function setupTables(): void {
    $pdo = getDB();

    // İletişim mesajları tablosu
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            name       VARCHAR(100)  NOT NULL,
            email      VARCHAR(150)  NOT NULL,
            subject    VARCHAR(200)  DEFAULT '',
            message    TEXT          NOT NULL,
            is_read    TINYINT(1)    DEFAULT 0,
            created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Projeler tablosu (JSON yerine DB'den de çekilebilir)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            title       VARCHAR(150)  NOT NULL,
            badge       VARCHAR(80)   NOT NULL,
            image       VARCHAR(255)  DEFAULT '',
            category    VARCHAR(50)   DEFAULT 'frontend',
            description TEXT          DEFAULT '',
            tags        VARCHAR(500)  DEFAULT '',
            github_url  VARCHAR(500)  DEFAULT '',
            created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Örnek proje verisi (sadece tablo boşsa ekle)
    $count = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    if ((int)$count === 0) {
        $pdo->exec("
            INSERT INTO projects (title, badge, image, category, description, tags, github_url) VALUES
            ('StreamAtlas', 'Web App', 'streamatlas.png', 'frontend',
             'Film ve dizi keşfetmeye yarayan modern bir streaming platformu arayüzü.',
             'HTML/CSS,JavaScript,API', 'https://github.com/OlcayKAHRAMAN2005'),
            ('GSWEB', 'ASP.NET', 'GSWEB_afiş.png', 'backend',
             'Galatasaray taraftar platformu. ASP.NET Core MVC ile geliştirilmiş içerik yönetim sistemi.',
             'C#,ASP.NET,SQL Server', 'https://github.com/OlcayKAHRAMAN2005'),
            ('Portfolio Site', 'Frontend', 'frontend.png', 'frontend',
             'Kişisel portfolyo web sitesi. Modern CSS, tema değiştirme ve dinamik içerik yönetimi.',
             'HTML,CSS,JavaScript', 'https://github.com/OlcayKAHRAMAN2005'),
            ('Mobile App', 'Flutter', 'mobile.png', 'mobile',
             'Flutter ile geliştirilen cross-platform mobil uygulama.',
             'Flutter,Dart,Mobile', 'https://github.com/OlcayKAHRAMAN2005');
        ");
    }
}

// =============================================
// YARDIMCI FONKSİYONLAR
// =============================================

// JSON yanıt gönder
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// POST verisini güvenli al
function post(string $key, string $default = ''): string {
    return trim($_POST[$key] ?? $default);
}

// JSON body'den veri al (fetch API için)
function jsonInput(): array {
    static $data = null;
    if ($data === null) {
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true) ?? [];
    }
    return $data;
}

// CORS başlıkları (geliştirme ortamı için)
function setCORSHeaders(): void {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
