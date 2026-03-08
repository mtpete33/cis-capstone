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

  $user = $_SESSION['user'];
  $roleID = (int)($user['roleID'] ?? 0);
  $userID = (int)($user['userID'] ?? 0);

  $title = trim((string)($_GET['title'] ?? ''));
  $statusID = (int)($_GET['statusID'] ?? 0);
  $priorityID = (int)($_GET['priorityID'] ?? 0);

  $sql = '
    SELECT
      wo."workOrderID",
      wo."title",
      wo."createdAt",
      l."locationName",
      s."statusName",
      p."priorityName"
    FROM work_orders wo
    JOIN locations l ON l."locationID" = wo."locationID"
    JOIN statuses s ON s."statusID" = wo."currentStatusID"
    JOIN priorities p ON p."priorityID" = wo."priorityID"
  ';

  $whereParts = [];
  $params = [];

  if ($roleID === 1) {
    // Admin sees all work orders
  } elseif ($roleID === 2) {
    $whereParts[] = '(wo."assignedToUserID" = :userID OR wo."assignedToUserID" IS NULL)';
    $params['userID'] = $userID;
  } elseif ($roleID === 3) {
    $whereParts[] = 'wo."submittedByUserID" = :userID';
    $params['userID'] = $userID;
  } else {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
  }

  if ($title !== '') {
    $whereParts[] = 'wo."title" ILIKE :title';
    $params['title'] = '%' . $title . '%';
  }

  if ($statusID > 0) {
    $whereParts[] = 'wo."currentStatusID" = :statusID';
    $params['statusID'] = $statusID;
  }

  if ($priorityID > 0) {
    $whereParts[] = 'wo."priorityID" = :priorityID';
    $params['priorityID'] = $priorityID;
  }

  if (!empty($whereParts)) {
    $sql .= ' WHERE ' . implode(' AND ', $whereParts);
  }

  $sql .= ' ORDER BY wo."createdAt" DESC LIMIT 100';

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll();

  echo json_encode([
    'ok' => true,
    'items' => $rows
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'Server error',
    'detail' => $e->getMessage()
  ]);
}