<?php
/**
 * api/certificates.php
 *
 * Endpoint'ler:
 *   GET  action=list              → Tüm sertifikaları listele
 *   GET  action=list&category=X   → Kategoriye göre filtrele
 *   POST action=add               → Sertifika ekle
 *   POST action=delete            → Sertifika sil
 */

session_start();
require_once __DIR__ . '/db.php';
setCORSHeaders();

// Sertifika tablosunu oluştur
function setupCertTable(): void {
    $pdo = getDB();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            title        VARCHAR(200)  NOT NULL,
            issuer       VARCHAR(150)  NOT NULL,
            category     VARCHAR(80)   NOT NULL DEFAULT 'other',
            date_earned  DATE          NOT NULL,
            credential_url VARCHAR(500) DEFAULT '',
            description  TEXT          DEFAULT '',
            created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Örnek veriler (tablo boşsa ekle)
    $count = $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
    if ((int)$count === 0) {
        $pdo->exec("
            INSERT INTO certificates (title, issuer, category, date_earned, credential_url, description) VALUES
            ('Web Geliştirme Temelleri', 'BTK Akademi', 'web', '2024-03-15', 'https://btkakademi.gov.tr', 'HTML, CSS ve JavaScript temellerini kapsayan 40 saatlik eğitim.'),
            ('C# ile Nesne Yönelimli Programlama', 'Udemy', 'backend', '2024-06-01', 'https://udemy.com', 'OOP prensipleri, SOLID, Design Patterns konularını içeren sertifika.'),
            ('ASP.NET Core MVC', 'Udemy', 'backend', '2024-08-20', 'https://udemy.com', 'ASP.NET Core ile tam kapsamlı web uygulaması geliştirme.'),
            ('Flutter & Dart ile Mobil Geliştirme', 'Udemy', 'mobile', '2024-11-10', 'https://udemy.com', 'Cross-platform mobil uygulama geliştirme temelleri.'),
            ('SQL ve Veritabanı Yönetimi', 'BTK Akademi', 'database', '2025-01-05', 'https://btkakademi.gov.tr', 'SQL sorgulama, ilişkisel veritabanı tasarımı ve optimizasyon.'),
            ('Git ve GitHub', 'BTK Akademi', 'tools', '2025-02-18', 'https://btkakademi.gov.tr', 'Sürüm kontrolü, branch yönetimi ve GitHub işbirlikleri.');
        ");
    }
}

setupCertTable();

$action = $_GET['action'] ?? (jsonInput()['action'] ?? 'list');

switch ($action) {

    case 'list':
        $category = $_GET['category'] ?? '';
        $pdo = getDB();
        if ($category && $category !== 'all') {
            $stmt = $pdo->prepare("SELECT * FROM certificates WHERE category = ? ORDER BY date_earned DESC");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM certificates ORDER BY date_earned DESC");
        }
        jsonResponse(['success' => true, 'certificates' => $stmt->fetchAll()]);
        break;

    case 'add':
        $input       = jsonInput();
        $title       = trim($input['title']          ?? '');
        $issuer      = trim($input['issuer']         ?? '');
        $category    = trim($input['category']       ?? 'other');
        $date_earned = trim($input['date_earned']    ?? date('Y-m-d'));
        $credential  = trim($input['credential_url'] ?? '');
        $description = trim($input['description']    ?? '');

        if (!$title || !$issuer) {
            jsonResponse(['success' => false, 'message' => 'Başlık ve veren kurum zorunludur.'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO certificates (title, issuer, category, date_earned, credential_url, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $issuer, $category, $date_earned, $credential, $description]);
        jsonResponse(['success' => true, 'message' => 'Sertifika eklendi.', 'id' => (int)$pdo->lastInsertId()]);
        break;

    case 'delete':
        $input = jsonInput();
        $id    = (int)($input['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'message' => 'Geçersiz ID.'], 400);

        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
        $stmt->execute([$id]);
        jsonResponse(['success' => true, 'message' => 'Sertifika silindi.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Geçersiz işlem.'], 400);
}
