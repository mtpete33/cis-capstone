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
  <header class="top-header">
    <div class="page-wrap">
  <h1 class="site-title">Work Order System</h1>
      <p class="user-info">Logged in as <strong><?= htmlspecialchars($user['email']) ?></strong></p>
    </div>
  </header>
  
<div class="subbar">
  <div class="page-wrap subbar-inner">
  <div>
    <span id="welcomeText">Welcome, </span>
    <span id="roleBadge" class="role-badge">User</span>
  </div>
  <a class="logout-btn" href="api/auth/logout.php">Logout</a>
  </div>
  </div>

  <main class="page-wrap">
    <section class="summary-row">
      <div class="summary-box">
        <span class="summary-label">Total Work Orders</span>
        <span class="summary-value" id="totalWO">-</span>
      </div>

      <div class="summary-box">
        <span class="summary-label">Open Work Orders</span>
       <span class="summary-value" id="openWO">-</span>
        </div>
    </section>

    <section class="dashboard-cards">
      <button class="dash-card active" data-panel="recentPanel" id="recentTab">
        <div class="dash-card-title">Recent Work Orders</div>
        <div class="dash-card-text"> View latest maintenance requests</div>
      </button>

      <button class="dash-card" data-panel="createPanel" id="createTab">
        <div class="dash-card-title">Create Work Order</div>
        <div class="dash-card-text">Submit a new maintenance request</div>
        </button>

       <button class="dash-card" data-panel="searchPanel" id="searchTab">
          <div class="dash-card-title">Search Work Orders</div>
          <div class="dash-card-text">Find and view existing requests</div>
          </button>
    </section>

    <section class="panel-shell">
      <div id="recentPanel" class="dashboard-panel active-panel">
        <h2>Recent Work Orders</h2>
        <p id="woStatus">Loading work orders...</p>

        <table class="wo-table">
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
          <tbody id="woTableBody"></tbody>
        </table>
      </div>

      <div id="createPanel" class="dashboard-panel hidden-panel">
        <h2>Create New Work Order</h2>

        <form id="createWorkOrderForm" class="wo-form">
          <div class="form-row">
            <label for="departmentID">Department</label>
            <select id="departmentID" name="departmentID" required>
              <option value="">Loading departments...</option>
            </select>
          </div>

          <div class="form-row">
            <label for="woTitle">Title</label>
            <input type="text" id="woTitle" name="title" required>
          </div>

          <div class="form-row">
            <label for="woDescription">Description</label>
            <textarea id="woDescription" name="description" rows="4"></textarea>
          </div>

          <div class="form-row">
            <label for="locationID">Location</label>
            <select id="locationID" name="locationID" required>
              <option value="">Loading locations...</option>
            </select>
          </div>

          <div class="form-row">
            <label for="priorityID">Priority</label>
            <select id="priorityID" name="priorityID" required>
              <option value="1">Low</option>
              <option value="2">Medium</option>
              <option value="3">High</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="primary-btn">Submit Work Order</button>
          </div>
        </form>

        <p id="formStatus"></p>
      </div>

      <div id="searchPanel" class="dashboard-panel hidden-panel">
        <h2>Search Work Orders</h2>

        <form id="searchWorkOrdersForm" class="wo-form">
          <div class="form-row">
            <label for="searchTitle">Title Keyword</label>
            <input type="text" id="searchTitle" name="searchTitle" placeholder="Enter title keyword">
          </div>
          
        <div class="form-row">
          <label for="searchStatus">Status</label>
          <select id="searchStatus" name="searchStatus">
            <option value="">-- Any Status --</option>
          </select>
        </div>
          
          <div class="form-row">
            <label for="searchPriority">Priority</label>
            <select id="searchPriority" name="searchPriority">
              <option value="">-- Any Priority --</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="primary-btn">Search</button>
          </div>
        </form>

        <p id="searchStatusMsg"></p>

        <table class="wo-table">
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
          <tbody id="searchTableBody"></tbody>
        </table>
      </div>
          
    </section>
  </main>
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="/assets/app.js"></script>
</body>

</html>


   