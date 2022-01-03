// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable();

  $('#pending-items').DataTable();
  $('#approved-items').DataTable();
  $('#reject-items').DataTable();
  $('#cancel-items').DataTable();
});

