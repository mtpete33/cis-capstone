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
        <title>Create Work Order</title>
         <link rel="stylesheet" href="/assets/styles.css">

   </head>
    <body>
      <h1>Create Work Order</h1>
      <p>Logged in as <strong><?= htmlspecialchars($user['email']) ?></strong></p>
    <p><a href="/">Back to Dashboard</a></p>

      <hr>

      <form id="createWorkOrderForm">
        <label>
          Department<br>
          <select id="departmentID" name="departmentID" required>
            <option value="">Loading departments...</option>
          </select>
        </label>
        <br><br>
        
        <label>
          Title<br>
          <input type="text" name="title" required>
        </label>
         <br><br>

         <label>
            Description<br>
            <textarea name="description" rows="4"></textarea>
             </label>
              <br><br>

      <label>
        Location<br>
        <select id="locationID" name="locationID" required>
          <option value="">Loading locations...</option>
        </select>
      </label>
       <br><br>

      <label>
        Priority<br>
        <select id="priorityID" name="priorityID">
          <option value="1">Low</option>
          <option value="2">Medium</option>
          <option value="3">High</option>
        </select>
      </label>
       <br><br>

        <button type="submit">Submit Work Order</button>
         </form>
          <p id="formStatus"></p>

      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

      <script>
      $(document).ready(function () {
        (async function init(){
          try {
            await Promise.all([loadDepartments(), loadLocations()]);
          } catch (e) {
            $('#formStatus').text('Failed to load form data.');
            console.error(e);
          }
        })();
        
        $('#createWorkOrderForm').on('submit', async function (e) {
          e.preventDefault();

          $('#formStatus').text('Submitting...');

          const payload = {
            title: $('input[name="title"]').val().trim(),
            description: $('textarea[name="description"]').val().trim(),
            locationID: parseInt($('#locationID').val(), 10),
            priorityID: parseInt($('#priorityID').val(), 10),
            departmentID: parseInt($('#departmentID').val(), 10) || null,
          };

          try {
            const res = await fetch('/api/work_orders/create.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
              $('#formStatus').text(data.error || 'Create failed');
              return;
            }

            $('#formStatus').text('Created! WorkOrderID = ' + data.workOrderID);
            // Optional: redirect to list page
            // window.location.href = '/workorders';
          } catch (err) {
            $('#formStatus').text('Network error');
          }
        });

        // escapeHtml
        function escapeHtml(str) {
          return String(str ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#39;');
        
        }

        //Populate select
        function populateSelect($select, items, valueKey, textKey, placeholder) {
          const opts = [`<option value="">${placeholder}</option>`];
          items.forEach((item) => {
            opts.push(`<option value="${escapeHtml(item[valueKey])}">${escapeHtml(item[textKey])}</option>`);
          });
          $select.html(opts.join(''));
        }

        //Populate departments
        async function loadDepartments() {
          const res = await fetch('/api/meta/departments.php', {credentials: 'include'});
           const data = await res.json();
           if (!res.ok || !data.ok) throw new Error(data.error || 'Failed to load departments');
           populateSelect($('#departmentID'), data.items, 'departmentID', 'departmentName', '--Select department--');
        }
        //Populate locations
        async function loadLocations() {
           const res = await fetch('/api/meta/locations.php', {credentials: 'include'});
           const data = await res.json();
           if (!res.ok || !data.ok) throw new Error(data.error || 'Failed to load locations');
           populateSelect($('#locationID'), data.items, 'locationID', 'locationName', '--Select location--');
        }
      });
      </script>

    </body>
</html>