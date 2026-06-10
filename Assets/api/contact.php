<?php
/**
 * api/contact.php
 *
 * Endpoint'ler:
 *   POST action=send    → Mesaj gönder (herkese açık)
 *   GET  action=list    → Mesajları listele (giriş gerekli)
 *   POST action=delete  → Mesaj sil (giriş gerekli)
 *   POST action=read    → Mesajı okundu işaretle (giriş gerekli)
 */

session_start();
require_once __DIR__ . '/db.php';
setCORSHeaders();
setupTables();

$action = $_GET['action'] ?? (jsonInput()['action'] ?? post('action', 'send'));

switch ($action) {

    // ---- MESAJ GÖNDER ----
    case 'send':
        $input   = jsonInput();
        $name    = trim($input['name']    ?? post('name'));
        $email   = trim($input['email']   ?? post('email'));
        $subject = trim($input['subject'] ?? post('subject', ''));
        $message = trim($input['message'] ?? post('message'));

        if (!$name || !$email || !$message) {
            jsonResponse(['success' => false, 'message' => 'Ad, e-posta ve mesaj zorunludur.'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Geçersiz e-posta adresi.'], 400);
        }
        if (strlen($message) > 5000) {
            jsonResponse(['success' => false, 'message' => 'Mesaj çok uzun (max 5000 karakter).'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $subject, $message]);

        jsonResponse([
            'success' => true,
            'message' => 'Mesajınız başarıyla gönderildi!'
        ]);
        break;

    // ---- MESAJLARI LİSTELE ----
    case 'list':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $pdo  = getDB();
        $stmt = $pdo->query(
            "SELECT id, name, email, subject, message, is_read, created_at
             FROM messages
             ORDER BY created_at DESC"
        );
        $msgs = $stmt->fetchAll();

        // Okunmamış mesaj sayısı
        $unread = array_filter($msgs, fn($m) => !$m['is_read']);

        jsonResponse([
            'success'  => true,
            'messages' => $msgs,
            'unread'   => count($unread)
        ]);
        break;

    // ---- MESAJ SİL ----
    case 'delete':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $input = jsonInput();
        $id    = (int)($input['id'] ?? post('id'));

        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Geçersiz mesaj ID.'], 400);
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$id]);

        jsonResponse(['success' => true, 'message' => 'Mesaj silindi.']);
        break;

    // ---- OKUNDU İŞARETLE ----
    case 'read':
        if (empty($_SESSION['user'])) {
            jsonResponse(['success' => false, 'message' => 'Giriş yapmanız gerekiyor.'], 401);
        }

        $input = jsonInput();
        $id    = (int)($input['id'] ?? post('id'));

        $pdo  = getDB();
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);

        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Geçersiz işlem.'], 400);
}
