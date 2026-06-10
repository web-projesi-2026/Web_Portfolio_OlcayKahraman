<?php
/**
 * api/projects.php
 *
 * Endpoint'ler:
 *   GET  action=list              → Tüm projeleri listele
 *   GET  action=list&category=X   → Kategoriye göre filtrele
 *   POST action=add               → Proje ekle (giriş gerekli)
 *   POST action=update            → Proje güncelle (giriş gerekli)
 *   POST action=delete            → Proje sil (giriş gerekli)
 */

session_start();
require_once __DIR__ . '/db.php';
setCORSHeaders();
setupTables();

$action   = $_GET['action']   ?? (jsonInput()['action'] ?? 'list');
$category = $_GET['category'] ?? '';

switch ($action) {

    // ---- PROJELERİ LİSTELE ----
    case 'list':
        $pdo = getDB();

        if ($category && $category !== 'all') {
            $stmt = $pdo->prepare(
                "SELECT * FROM projects WHERE category = ? ORDER BY created_at DESC"
            );
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        }

        $projects = $stmt->fetchAll();

        // tags alanını dizi'ye çevir
        foreach ($projects as &$p) {
            $p['tags'] = array_filter(array_map('trim', explode(',', $p['tags'])));
            $p['tags'] = array_values($p['tags']);
        }

        jsonResponse(['success' => true, 'projects' => $projects]);
        break;

    // ---- PROJE EKLE ----
    case 'add':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $input = jsonInput();
        $title       = trim($input['title']       ?? '');
        $badge       = trim($input['badge']       ?? '');
        $image       = trim($input['image']       ?? '');
        $category_in = trim($input['category']    ?? 'frontend');
        $description = trim($input['description'] ?? '');
        $tags        = trim($input['tags']        ?? '');
        $github_url  = trim($input['github_url']  ?? '');

        if (!$title || !$badge) {
            jsonResponse(['success' => false, 'message' => 'Başlık ve badge zorunludur.'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO projects (title, badge, image, category, description, tags, github_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $badge, $image, $category_in, $description, $tags, $github_url]);

        jsonResponse([
            'success' => true,
            'message' => 'Proje eklendi.',
            'id'      => (int)$pdo->lastInsertId()
        ]);
        break;

    // ---- PROJE GÜNCELLE ----
    case 'update':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $input = jsonInput();
        $id          = (int)($input['id'] ?? 0);
        $title       = trim($input['title']       ?? '');
        $badge       = trim($input['badge']       ?? '');
        $image       = trim($input['image']       ?? '');
        $category_in = trim($input['category']    ?? 'frontend');
        $description = trim($input['description'] ?? '');
        $tags        = trim($input['tags']        ?? '');
        $github_url  = trim($input['github_url']  ?? '');

        if (!$id || !$title) {
            jsonResponse(['success' => false, 'message' => 'ID ve başlık zorunludur.'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "UPDATE projects
             SET title=?, badge=?, image=?, category=?, description=?, tags=?, github_url=?
             WHERE id=?"
        );
        $stmt->execute([$title, $badge, $image, $category_in, $description, $tags, $github_url, $id]);

        jsonResponse(['success' => true, 'message' => 'Proje güncellendi.']);
        break;

    // ---- PROJE SİL ----
    case 'delete':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $input = jsonInput();
        $id    = (int)($input['id'] ?? 0);

        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Geçersiz proje ID.'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);

        jsonResponse(['success' => true, 'message' => 'Proje silindi.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Geçersiz işlem.'], 400);
}
