<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
startSession();

if (!isset($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

try {
  $pdo = getPDO();
  $stmt = $pdo->query('
  SELECT "locationID", "locationName"
  FROM locations
  WHERE "isActive" = TRUE
  ORDER BY "locationName" ASC
  ');

  echo json_encode(['ok' => true, 'items' => $stmt->fetchAll()]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
