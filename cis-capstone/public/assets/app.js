$(document).ready(function () {

  let createFormDataLoaded = false;
  let searchFormDataLoaded = false;

  // Click handlers
  $(document).on('click', '.dash-card', function () {
    const panelId = $(this).data('panel');
    showPanel(panelId);
  })


  // Helper functions
  function loadStatuses() {
    return fetch('/api/meta/statuses.php', { credentials: 'include' })
    .then(res => res.json())
    .then(data =>{
      if (!data.ok) throw new Error(data.error || 'Failed to load statuses');
      populateSelect($('#searchStatus'), data.items, 'statusID', 'statusName', '-- Any status --');
    });
  }

  function loadPriorities() {
    return fetch('/api/meta/priorities.php', { credentials: 'include' })
    .then(res => res.json())
    .then(data =>{
      if (!data.ok) throw new Error(data.error || 'Failed to load priorities');
      populateSelect($('#searchPriority'), data.items, 'priorityID', 'priorityName', '-- Any priority --');
    });
  }

  function loadSearchFormData() {
    $('#searchStatusMsg').text('Loading search filters...');
    Promise.all([loadStatuses(), loadPriorities()])
    .then(function () {
      searchFormDataLoaded = true;
      $('#searchStatusMsg').text('');
    })
    .catch(function (err) {
      $('#searchStatusMsg').text('Failed to load search filters.');
      console.error(err);
    })
  }

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

  function getRoleLabel(roleID) {
    if (roleID === 1) return 'Admin';
    if (roleID === 2) return 'Technician';
    if (roleID === 3) return 'Requester';
    return 'User';
  }

  function showPanel(panelId) {
    $('.dashboard-panel').removeClass('active-panel').addClass('hidden-panel');
    $('.dash-card').removeClass('active');
    $('#' + panelId).removeClass('hidden-panel').addClass('active-panel');
    $('.dash-card[data-panel="' + panelId + '"]').addClass('active');

    if (panelId === 'createPanel' && !createFormDataLoaded) {
      loadCreateFormData();
    }

    if (panelId === 'createPanel') {
      $('#formStatus').text('');
    }

    if (panelId === 'searchPanel' && !searchFormDataLoaded) {
      loadSearchFormData();
    }
    
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

  function populateSelect($select, items, valueKey, textKey, placeholder) {
    const opts = [`<option value="">${escapeHtml(placeholder)}</option>`];

    (items || []).forEach(function (item) {
      opts.push(
        `<option value="${escapeHtml(item[valueKey])}">${escapeHtml(item[textKey])}</option>`
      );
    });

    $select.html(opts.join(''));
  }

  function loadDepartments() {
    return fetch('/api/meta/departments.php', { credentials: 'include' })
      .then(res => res.json())
      .then(data => {
        if (!data.ok) throw new Error(data.error || 'Failed to load departments');
        populateSelect($('#departmentID'), data.items, 'departmentID', 'departmentName', '-- Select department --');
      });
  }

  function loadLocations() {
    return fetch('/api/meta/locations.php', { credentials: 'include' })
      .then(res => res.json())
      .then(data => {
        if (!data.ok) throw new Error(data.error || 'Failed to load locations');
        populateSelect($('#locationID'), data.items, 'locationID', 'locationName', '-- Select location --');
      });
  }

  function loadCreateFormData() {
    $('#formStatus').text('Loading form data...');

    Promise.all([loadDepartments(), loadLocations()])
      .then(function () {
        createFormDataLoaded = true;
        $('#formStatus').text('');
      })
      .catch(function (err) {
        $('#formStatus').text('Failed to load form data.');
        console.error(err);
      });
  }

  $(document).on('submit', '#createWorkOrderForm', function (e) {
    e.preventDefault();

    $('#formStatus').text('Submitting...');

    const payload = {
      title: $('#woTitle').val().trim(),
      description: $('#woDescription').val().trim(),
      locationID: parseInt($('#locationID').val(), 10),
      priorityID: parseInt($('#priorityID').val(), 10),
      departmentID: parseInt($('#departmentID').val(), 10)
    };

    fetch('/api/work_orders/create.php', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })
      .then(res => res.json())
      .then(data => {
        if (!data.ok) {
          $('#formStatus').text(data.error || 'Failed to create work order');
          return;
        }

        $('#formStatus').text('Created! WorkOrderID = ' + data.workOrderID);

        $('#createWorkOrderForm')[0].reset();

        loadWorkOrders();
        showPanel('recentPanel');
      })
      .catch(function (err) {
        $('#formStatus').text('Failed to create work order (network/server error)');
        console.error(err);
      });
  });

  // Search form submission
  $(document).on('submit', '#searchWorkOrdersForm', function (e) {
    e.preventDefault();

    $('#searchStatusMsg').text('Searching...');
    const params = new URLSearchParams();

    const title = $('#searchTitle').val().trim();
    const statusID = $('#searchStatus').val();
    const priorityID = $('#searchPriority').val();

    if (title != '') params.append('title', title);
    if (statusID != '') params.append('statusID', statusID);
    if (priorityID != '') params.append('priorityID', priorityID);

    fetch('/api/work_orders/search.php?' + params.toString(), {
      credentials: 'include'
    })
    .then(res => res.json())
    .then(data => {
      if (!data.ok) {
        $('#searchStatusMsg').text(data.error || 'Failed to search work orders');
        return;
      }

      const rows = data.items || [];

      if (rows.length === 0) {
        $('#searchStatusMsg').text('No work orders found.');
        $('#searchResultsBody').html('');
        return;
      }
      $('#searchStatusMsg').text('');

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

      $('#searchTableBody').html(html);
    })
    .catch(function (err) {
      $('#searchStatusMsg').text('Failed to search work orders (network/server error)');
      console.error(err);
    });
  });
  
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

     // $('#whoami').text(
     //   'Logged in as ' + res.user.email + ' (roleID: ' + res.user.roleID + ')'
     // );

     $('#welcomeText').text('Welcome, ' + (res.user.userName || res.user.email));
     $('#roleBadge').text(getRoleLabel(res.user.roleID));
     

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