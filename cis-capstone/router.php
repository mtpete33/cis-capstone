<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $uri  = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH) ?? '/';
if ($path === '') $path = '/';

    $publicRoot = __DIR__ . '/public';

    $fullPublicPath = realpath($publicRoot . $path);
    if ($path !== '/' && $fullPublicPath && str_starts_with($fullPublicPath, realpath($publicRoot)) && is_file($fullPublicPath)) {
      return false;
    }


// API routing
if (str_starts_with($path, '/api/')) {
  $apiFile = __DIR__ . $path;

  if (file_exists($apiFile) && !is_dir($apiFile)) {
    require $apiFile;
    exit;
  }

  http_response_code(404);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'ok' => false,
    'error' => 'API endpoint not found',
    'path' => $path,
    'expectedFile' => $apiFile
  ]);
  exit;
}

// Page routing
if ($path === '/login' || $path === '/login.php') {
  require __DIR__ . '/public/login.php';
  exit;
}

// New work order page
if ($path === '/workorders/new') {
  require __DIR__ . '/public/workorders_new.php';
  exit;
}

    // Default route -> dashboard
    require __DIR__ . '/public/index.php';
