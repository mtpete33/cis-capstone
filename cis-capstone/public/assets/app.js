$(document).ready(function () {

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';

    const d = new Date(dateStr);

    return d.toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit'
    });
  }

  function loadWorkOrders() {
    $.getJSON('/api/work_orders/list.php')
      .done(function (d) {
        if (!d.ok) {
          $('#woStatus').text('Failed to load work orders');
          return;
        }

        const rows = d.items || [];
        if (rows.length === 0) {
          $('#woStatus').text('No work orders yet.');
          $('#woTableBody').html('');
          return;
        }

        $('#woStatus').text('');
        const html = rows.map(function (wo) {
          return `
            <tr>
              <td>${escapeHtml(wo.workOrderID)}</td>
              <td>${escapeHtml(wo.title)}</td>
              <td>${escapeHtml(wo.statusName)}</td>
              <td>${escapeHtml(wo.priorityName)}</td>
              <td>${escapeHtml(wo.locationName)}</td>
              <td>${escapeHtml(formatDate(wo.createdAt))}</td>
            </tr>
          `;
        }).join('');

        $('#woTableBody').html(html);
      })
      .fail(function () {
        $('#woStatus').text('Failed to load work orders (network/server error)');
      });
  }
  
  $.ajax({
    url: '/api/auth/check.php',
    method: 'GET',
    dataType: 'json',
    xhrFields: {
      withCredentials: true
    }
  })
   .done(function (res) {
     if (!res.loggedIn) {
       window.location.href = '/login';
       return;
     }

     $('#whoami').text(
       'Logged in as ' + res.user.email + ' (roleID: ' + res.user.roleID + ')'
     );

     $.getJSON('/api/dashboard/summary.php')
     .done(function (d){
       if (!d.ok) return;
       $('#totalWO').text(d.total);
       $('#openWO').text(d.open);
       $('#dashStatus').text('');
     })
     .fail(function () {
       $('#dashStatus').text('Failed to load dashboard summary');
     });
     
     loadWorkOrders();
     
   })
    .fail(function (){
    //Safety fallback
    window.location.href = '/login'
  });
  
});