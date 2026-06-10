<?php
/**
 * api/auth.php
 * Bu bölüm kaldırılmıştır.
 */
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => false, 'message' => 'Bu endpoint artık kullanılmamaktadır.'], JSON_UNESCAPED_UNICODE);
exit;
