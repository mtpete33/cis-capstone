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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
$title =  trim((string)($data['title'] ?? ''));
$description = trim((string)($data['description'] ?? ''));
$locationID = (int)($data['locationID'] ?? 0);
$priorityID = (int)($data['priorityID'] ?? 0);
$departmentID = (int)($data['departmentID'] ?? 0);

$fields = [];
if ($title === '') $fields['title'] = 'Title is required';
if ($locationID <= 0) $fields['locationID'] = 'Location is required';
if ($priorityID <= 0) $fields['priorityID'] = 'Priority is required';
if ($departmentID <= 0) $fields['departmentID'] = 'Department is required';

if ($fields) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Validation error', 'fields' => $fields]);
  exit;
}

$submittedByUserID = (int)($_SESSION['user']['userID'] ?? 0);
if ($submittedByUserID <= 0) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

try {
  $pdo = getPDO();

  //Validate department exists and is active
  $deptStmt = $pdo->prepare('SELECT 1 FROM departments WHERE "departmentID" = :id AND "isActive" = TRUE');
  $deptStmt->execute(['id' => $departmentID]);
  if (!$deptStmt->fetchColumn()) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Validation error', 'fields' => ['departmentID' => 'Invalid department']]);
  }

  // Validate location exists and is active
  $locStmt = $pdo->prepare('SELECT 1 FROM locations WHERE "locationID" = :id AND "isActive" = TRUE');
  $locStmt->execute(['id' => $locationID]);
  if (!$locStmt->fetchColumn()) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Validation error', 'fields' => ['locationID' => 'Invalid location']]);
  }

  $statusID = (int)$pdo->query('SELECT "statusID" FROM statuses ORDER BY "statusID" ASC LIMIT 1')->fetchColumn();
  if ($statusID <= 0) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No statuses found']);
    exit;
  }

  $stmt = $pdo->prepare(
    'INSERT INTO work_orders
    ("title", "description", "locationID", "priorityID", "departmentID", "currentStatusID", "submittedByUserID")
    VALUES (:title, :description, :locationID, :priorityID, :departmentID, :statusID, :submittedByUserID)
    RETURNING "workOrderID"'
  );

  $stmt->execute([
                   'title' => $title,
                   'description' => $description,
                   'locationID' => $locationID,
                   'priorityID' => $priorityID,
                   'departmentID' => $departmentID,
                   'statusID' => $statusID,
                   'submittedByUserID' => $submittedByUserID,

                 ]);

  $newID = (int)$stmt->fetchColumn();

  echo json_encode(['ok' => true, 'workOrderID' => $newID]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
                   'ok' => false, 
                   'error' => 'Server error', 
                   'detail' => $e->getMessage(),
                   ]);
}