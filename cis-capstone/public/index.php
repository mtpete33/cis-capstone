<?php 
require_once __DIR__ . '/../config/session.php';
requireLogin();
$user = currentUser();
?>

<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" href="data:,">
   <title>Work Order System</title>
  
   
   <link rel="stylesheet" href="/assets/styles.css">

</head>

<body>
  <h1>Work Order System</h1>
  <p id="whoami">Checking session...</p>
  <p><a href="/api/auth/logout.php">Logout</a></p>
  <hr>

  <div id="dashboard">
    <p id="status">Loading dashboard...</p>
    <ul>
      <li>Total work orders: <span id="totalWO">-</span></li>
      <li>Open work orders: <span id="openWO">-</span></li>
    </ul>
    
    <h2>Create New Work Order</h2>
    <p><a href="/workorders/new">+ Create New Work Order</a></p>

    <h2>Recent Work Orders</h2>
    <p id="woStatus">Loading work orders...</p>
    <table border="1" cellpadding="6" cellspacing="0" style="width:100%; max-width:1000px;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Location</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody id="woTableBody">  </tbody>
    </table>
  </div>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="/assets/app.js"></script>
</body>

</html>


   