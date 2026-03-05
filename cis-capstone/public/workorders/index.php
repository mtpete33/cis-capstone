<?php
require_once __DIR__ . '/../../config/session.php';
requireLogin();
$user = currentUser();
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Work Orders</title>

      <link rel="stylesheet" href="/assets/styles.css">

   </head>

  <body>
    <h1>Work Orders</h1>
    <p>Logged in as <strong><?= htmlspecialchars($user['email']) ?></strong></p>
    <p><a href="/">Back to Dashboard</a></p>
    <p><a href="/workorders/new">+ Create New Work Order</a></p>
    <hr>

    <table border="1" cellpadding="8" id="workOrdersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Location</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6">Loading...</td>
        </tr>
      </tbody>
    </table>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
      $(document).ready(function (){
        loadWorkOrders();

      });

      async function loadWorkOrders(){
        try {
          const res = await fetch('/api/work_orders/list.php', {credentials: 'include'});
          const data = await res.json();

          if(!res.ok || !data.ok){
            $('#workOrdersTable tbody').html('<tr><td colspan="6">Failed to load work orders</td></tr>');
            return;
          }
          const rows = data.items;

          if(rows.length === 0){
             $('#workOrdersTable tbody').html('<tr><td colspan="6">No work orders found</td></tr>');
             return;
          }
          let html = '';
          rows.forEach(row => {
            html += `
            <tr>
            <td>${row.workOrderID}</td>
            <td>${escapeHtml(row.title)}</td>
            <td>${escapeHtml(row.locationName)}</td>
            <td>${escapeHtml(row.priorityName)}</td>
            <td>${escapeHtml(row.statusName)}</td>
            <td>${escapeHtml(formatDate(row.createdAt))}</td>
            </tr>
            `;
          });
          $('#workOrdersTable tbody').html(html);
          }
         catch (err){
           console.error(err);
           $('#workOrdersTable tbody').html('<tr><td colspan="6">Network error</td></tr>');
         }
        }


      function escapeHtml(str){
        return String(str ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
      }

      function formatDate(dateStr) {
        const d= new Date(dateStr);
        return d.toLocaleString();
      }

    </script>
      
  </body>
</html>