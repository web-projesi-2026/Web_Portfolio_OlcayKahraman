<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum | Olcay KAHRAMAN Portfolio</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0a192f;
            color: #ccd6f6;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .card {
            background: #112240; border-radius: 16px;
            padding: 40px; max-width: 680px; width: 100%;
            border: 1px solid rgba(100,255,218,0.15);
        }
        h1 { color: #64ffda; font-size: 1.8rem; margin-bottom: 8px; }
        .subtitle { color: #8892b0; margin-bottom: 32px; }
        .step {
            background: #0a192f; border-radius: 10px; padding: 18px 20px;
            margin-bottom: 14px; border-left: 3px solid #64ffda;
        }
        .step h3 { font-size: 1rem; margin-bottom: 6px; color: #64ffda; }
        .step p, .step pre { font-size: 0.9rem; color: #8892b0; line-height: 1.6; }
        pre { background: #020c1b; padding: 12px; border-radius: 8px; margin-top: 8px; overflow-x:auto; }
        code { color: #64ffda; font-family: monospace; }
        .status { padding: 14px 18px; border-radius: 10px; margin-top: 24px; font-weight: 600; }
        .success { background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid #34d399; }
        .error   { background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid #f87171; }
        .warning { background: rgba(251,191,36,0.15);  color: #fbbf24; border: 1px solid #fbbf24; }
        .btn {
            display: inline-block; margin-top: 24px; padding: 12px 28px;
            background: #64ffda; color: #0a192f; border-radius: 50px;
            text-decoration: none; font-weight: 700; font-size: 0.95rem;
        }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { padding: 8px 12px; text-align:left; border-bottom: 1px solid rgba(255,255,255,0.05); font-size:0.85rem; }
        th { color: #64ffda; }
        .ok   { color: #34d399; }
        .fail { color: #f87171; }
    </style>
</head>
<body>
<div class="card">
    <h1>🚀 Portfolio Kurulum Sihirbazı</h1>
    <p class="subtitle">Veritabanı bağlantısı ve tabloları otomatik kurulur.</p>

<?php
require_once __DIR__ . '/api/db.php';

$errors   = [];
$warnings = [];
$success  = false;

// ---- PHP Sürüm Kontrolü ----
$phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');

// ---- PDO MySQL Kontrolü ----
$pdoOk = extension_loaded('pdo_mysql');

// ---- Bağlantı Testi ----
$connOk = false;
$connMsg = '';
if ($phpOk && $pdoOk) {
    try {
        // Önce DB olmadan bağlan
        $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        // Veritabanını oluştur (yoksa)
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME . "`");
        $connOk  = true;
        $connMsg = 'Bağlantı başarılı!';
    } catch (PDOException $e) {
        $connMsg = $e->getMessage();
        $errors[] = $connMsg;
    }
}

// ---- Tabloları Oluştur ----
$tablesOk = false;
if ($connOk) {
    try {
        setupTables();
        $tablesOk = true;
    } catch (Exception $e) {
        $errors[] = 'Tablo oluşturma hatası: ' . $e->getMessage();
    }
}

$success = $phpOk && $pdoOk && $connOk && $tablesOk;
?>

    <!-- SİSTEM KONTROLÜ -->
    <div class="step">
        <h3>🔍 Sistem Kontrolü</h3>
        <table>
            <tr>
                <th>Gereksinim</th>
                <th>Durum</th>
                <th>Değer</th>
            </tr>
            <tr>
                <td>PHP Sürümü (&ge; 7.4)</td>
                <td class="<?= $phpOk ? 'ok' : 'fail' ?>"><?= $phpOk ? '✅ OK' : '❌ Hata' ?></td>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <td>PDO MySQL Uzantısı</td>
                <td class="<?= $pdoOk ? 'ok' : 'fail' ?>"><?= $pdoOk ? '✅ OK' : '❌ Eksik' ?></td>
                <td><?= $pdoOk ? 'Yüklü' : 'Eksik — php.ini\'de etkinleştirin' ?></td>
            </tr>
            <tr>
                <td>MySQL Bağlantısı</td>
                <td class="<?= $connOk ? 'ok' : 'fail' ?>"><?= $connOk ? '✅ OK' : '❌ Hata' ?></td>
                <td><?= htmlspecialchars($connMsg) ?></td>
            </tr>
            <tr>
                <td>Tablolar</td>
                <td class="<?= $tablesOk ? 'ok' : 'fail' ?>"><?= $tablesOk ? '✅ Oluşturuldu' : '❌ Hata' ?></td>
                <td><?= $tablesOk ? 'messages, projects' : 'Başarısız' ?></td>
            </tr>
        </table>
    </div>

    <!-- VERİTABANI BİLGİLERİ -->
    <div class="step">
        <h3>⚙️ Bağlantı Bilgileri (api/db.php)</h3>
        <p>Aşağıdaki değerleri kendi ortamınıza göre güncelleyin:</p>
        <pre><code>define('DB_HOST', '<?= DB_HOST ?>');
define('DB_USER', '<?= DB_USER ?>');
define('DB_PASS', '***');
define('DB_NAME', '<?= DB_NAME ?>');</code></pre>
    </div>

    <!-- SONUÇ -->
    <?php if ($success): ?>
    <div class="status success">
        ✅ Kurulum başarıyla tamamlandı! Veritabanı ve tablolar hazır.
    </div>
    <a href="index.html" class="btn">🏠 Siteye Git</a>

    <div class="step" style="margin-top:24px; border-left-color:#f87171;">
        <h3>⚠️ Güvenlik Uyarısı</h3>
        <p>Kurulum tamamlandıktan sonra <code>install.php</code> dosyasını silin veya erişimi engelleyin.</p>
        <pre><code>rm install.php</code></pre>
    </div>

    <?php else: ?>
    <div class="status error">
        ❌ Kurulum tamamlanamadı. Hataları düzeltin ve sayfayı yenileyin.
        <?php foreach ($errors as $err): ?>
        <br><small><?= htmlspecialchars($err) ?></small>
        <?php endforeach; ?>
    </div>

    <div class="step" style="margin-top:20px;">
        <h3>🛠️ XAMPP / WAMP Kullanıcıları İçin</h3>
        <p>1. XAMPP Control Panel'den <strong>Apache</strong> ve <strong>MySQL</strong>'i başlatın.<br>
           2. <code>api/db.php</code> dosyasındaki bilgileri kontrol edin.<br>
           3. Bu sayfayı yenileyin.</p>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
